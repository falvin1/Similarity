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
from flask_sqlalchemy import SQLAlchemy

db = SQLAlchemy()  # Keep this definition, but initialize it with the app later

# Definisikan model untuk tabel reference_documents
class ReferenceDocument(db.Model):
    __tablename__ = 'reference_documents'  # Nama tabel yang sesuai di database Anda
    id = db.Column(db.Integer, primary_key=True)
    title = db.Column(db.String(255))
    preprocessed_content = db.Column(db.Text)

    def __repr__(self):
        return f"<ReferenceDocument {self.title}>"
    
app = Flask(__name__)
CORS(app)
app.config['SQLALCHEMY_DATABASE_URI'] = 'mysql+mysqlconnector://root:@localhost/testing'
app.config['SQLALCHEMY_TRACK_MODIFICATIONS'] = False

db.init_app(app)  # Initialize the db with the app here

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
    if 'file' not in request.files:
        return jsonify({'error': 'No file uploaded'}), 400

    file = request.files['file']

    if file.filename == '':
        return jsonify({'error': 'No selected file'}), 400

    try:
        # Ekstrak dan preprocess file yang diupload
        uploaded_text = extract_text_from_pdf(file)  # Gunakan fungsi extract_text_from_pdf
        if uploaded_text is None:
            return jsonify({'error': 'Failed to extract text from PDF'}), 500

        uploaded_text = preprocess_text(uploaded_text)

        # Ambil referensi dari tabel reference_documents di database
        references_data = ReferenceDocument.query.all()  # Mengambil semua data referensi dari tabel reference_documents

        if not references_data:
            return jsonify({'error': 'No references found in the database'}), 400

        # Ambil preprocessed_content dari setiap referensi
        preprocessed_refs = [ref.preprocessed_content for ref in references_data]

        # Gabungkan dokumen user + semua referensi
        all_texts = [uploaded_text] + preprocessed_refs

        # Hitung TF-IDF
        vectorizer = TfidfVectorizer()
        tfidf_matrix = vectorizer.fit_transform(all_texts)

        # Hitung cosine similarity
        similarities = cosine_similarity(tfidf_matrix[0:1], tfidf_matrix[1:]).flatten()

        # Buat response
        comparisons = []
        for i, ref in enumerate(references_data):
            comparisons.append({
                'reference_id': ref.id,
                'reference_title': ref.title,
                'similarity': round(similarities[i] * 100, 2),
            })

        return jsonify({
            'message': 'Plagiarism check completed',
            'comparisons': comparisons
        })

    except Exception as e:
        print(f"❌ ERROR in check_plagiarism: {str(e)}")
        return jsonify({'error': str(e)}), 500


if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000, debug=True)
