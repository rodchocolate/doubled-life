#!/usr/bin/env python3
"""
DOUBLED.LIFE - Modular Flask Server
Designed for GoDaddy Shared Hosting (Python/CGI compatible)

Structure:
- root/
  - app.py (this file - main entry point)
  - modules/
    - music.py     - Music/YouTube/Streaming APIs
    - files.py     - File handling (xlsx, docx, pdf, etc.)
    - tools.py     - Calculators, utilities
    - affiliates.py - Affiliate link management
  - static/        - CSS, JS, images
  - templates/     - HTML templates (Jinja2)
  - data/          - JSON data files

Run locally: python app.py
GoDaddy: Configure as CGI/WSGI application
"""

from flask import Flask, request, jsonify, send_from_directory, render_template
from flask_cors import CORS
import os
import json

# Module imports
from modules.music import music_bp
from modules.files import files_bp
from modules.tools import tools_bp
from modules.affiliates import affiliates_bp

# Initialize Flask app
app = Flask(__name__, 
    static_folder='static',
    template_folder='templates'
)
app.secret_key = os.environ.get('FLASK_SECRET', 'dev-secret-change-me')
CORS(app, supports_credentials=True)

# Register blueprints (modular routes)
app.register_blueprint(music_bp, url_prefix='/api/music')
app.register_blueprint(files_bp, url_prefix='/api/files')
app.register_blueprint(tools_bp, url_prefix='/api/tools')
app.register_blueprint(affiliates_bp, url_prefix='/api/affiliates')

# ==========================================
# Static File Serving
# ==========================================

@app.route('/')
def index():
    """Serve main landing page"""
    return send_from_directory('.', 'index.html')

@app.route('/<path:filename>')
def serve_static(filename):
    """Serve any static file"""
    if filename.endswith('.html') or filename.endswith('.css') or \
       filename.endswith('.js') or filename.endswith('.json'):
        if os.path.exists(filename):
            return send_from_directory('.', filename)
    return 'Not found', 404

@app.route('/static/<path:filename>')
def serve_static_assets(filename):
    """Serve static assets"""
    return send_from_directory('static', filename)

@app.route('/backgrounds/<path:filename>')
def serve_backgrounds(filename):
    """Serve background images"""
    return send_from_directory('backgrounds', filename)

# ==========================================
# Health Check
# ==========================================

@app.route('/api/health')
def health():
    """Health check endpoint"""
    return jsonify({
        'status': 'ok',
        'service': 'doubled.life',
        'modules': ['music', 'files', 'tools', 'affiliates']
    })

# ==========================================
# Error Handlers
# ==========================================

@app.errorhandler(404)
def not_found(e):
    return jsonify({'error': 'Not found'}), 404

@app.errorhandler(500)
def server_error(e):
    return jsonify({'error': 'Server error'}), 500

# ==========================================
# Run Server
# ==========================================

if __name__ == '__main__':
    port = int(os.environ.get('PORT', 8080))
    debug = os.environ.get('FLASK_ENV', 'development') == 'development'
    
    print(f"""
╔════════════════════════════════════════════╗
║         DOUBLED.LIFE Server                ║
║     http://localhost:{port}                  ║
╚════════════════════════════════════════════╝
    """)
    
    app.run(host='0.0.0.0', port=port, debug=debug)
