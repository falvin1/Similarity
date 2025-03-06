from flask import Flask, request, jsonify
import os
import string
import json
import numpy as np
from pdfminer.high_level import extract_text
from werkzeug.utils import secure_filename
import nltk
from nltk.tokenize import word_tokenize
from nltk.corpus import stopwords
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.metrics.pairwise import cosine_similarity
from flask_cors import CORS
from Sastrawi.Stemmer.StemmerFactory import StemmerFactory

app = Flask(__name__)
CORS(app)

nltk.download('punkt')
nltk.download('stopwords')

factory = StemmerFactory()
stemmer = factory.create_stemmer()

# Folder penyimpanan sementara
if not os.path.exists("temp"):
    os.makedirs("temp")

def extract_text_from_pdf(file):
    try:
        file_path = os.path.join("temp", secure_filename(file.filename))
        file.save(file_path)
        text = extract_text(file_path)
        os.remove(file_path)
        return text
    except Exception as e:
        print(f"❌ ERROR di extract_text_from_pdf: {str(e)}")  # Debugging
        return None  # Jika error, return None

def preprocess_text(text):
    text = text.lower()
    text = text.translate(str.maketrans("", "", string.punctuation))
    tokens = word_tokenize(text)
    stop_words = set(stopwords.words('indonesian'))
    tokens = [word for word in tokens if word not in stop_words]
    stemmed_tokens = [stemmer.stem(word) for word in tokens]
    return ' '.join(stemmed_tokens)

@app.route('/preprocess', methods=['POST'])
def preprocess():
    if 'file' not in request.files:
        return jsonify({'error': 'No file uploaded'}), 400

    file = request.files['file']
    
    if file.filename == '':
        return jsonify({'error': 'No selected file'}), 400

    try:
        print(f"📂 File diterima: {file.filename}")  # Debugging
        original_text = extract_text_from_pdf(file)
        if original_text is None:
            return jsonify({'error': 'Gagal mengekstrak teks dari PDF'}), 500
        preprocessed_text = preprocess_text(original_text)

        return jsonify({
            'original_text': original_text,
            'preprocessed_text': preprocessed_text
        })

    except Exception as e:
        return jsonify({'error': str(e)}), 500


@app.route('/check-plagiarism', methods=['POST'])
def check_plagiarism():
    # print("Received Headers:", request.headers)
    # print("Received Files:", request.files)
    # print("Received Form:", request.form)
    # print("Received JSON:", request.get_json(silent=True))

    if 'file' not in request.files:
        return jsonify({'error': 'No file uploaded'}), 400

    file = request.files['file']

    if file.filename == '':
        return jsonify({'error': 'No selected file'}), 400

    try:
        uploaded_text = extract_text_from_pdf(file)
        if uploaded_text is None:
            return jsonify({'error': 'Failed to extract text from PDF'}), 500

        uploaded_text = preprocess_text(uploaded_text)

        # Ambil references dari request form
        references_data = request.form.get('references', None) 
        print(f"References data: {references_data}")
        if not references_data:
            return jsonify({'error': 'References are required'}), 400

        try:
            references_data = json.loads(references_data)['references']
        except (json.JSONDecodeError, TypeError) as e:
            print(f"❌ ERROR parsing references: {str(e)}")  # Debugging
            return jsonify({'error': 'Invalid references JSON'}), 400

        all_texts = [uploaded_text] + [preprocess_text(ref['content']) for ref in references_data]

        vectorizer = TfidfVectorizer()
        tfidf_matrix = vectorizer.fit_transform(all_texts)

        uploaded_vector = tfidf_matrix[0]

        comparisons = []

        for i, ref in enumerate(references_data):
            ref_id = ref['id']
            ref_title = ref['title']
            ref_vector = tfidf_matrix[i + 1]

            similarity_score = cosine_similarity(uploaded_vector, ref_vector)[0][0]

            comparisons.append({
                'reference_id': ref_id,
                'reference_title': ref_title,
                'similarity': round(similarity_score * 100, 2),
            })

        return jsonify({
            'message': 'Plagiarism check completed',
            'comparisons': comparisons
        })

    except Exception as e:
        print(f"❌ ERROR in check_plagiarism: {str(e)}")  # Debugging
        return jsonify({'error': str(e)}), 500

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000, debug=True)
