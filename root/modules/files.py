"""
Files Module - Handle common file types
xlsx, docx, pdf, csv, txt, images
"""

from flask import Blueprint, request, jsonify, send_file
import os
import io
import json
from datetime import datetime

files_bp = Blueprint('files', __name__)

# Supported file types
SUPPORTED_TYPES = {
    'documents': ['.pdf', '.docx', '.doc', '.txt', '.rtf'],
    'spreadsheets': ['.xlsx', '.xls', '.csv'],
    'images': ['.png', '.jpg', '.jpeg', '.gif', '.webp', '.svg'],
    'data': ['.json', '.xml', '.yaml', '.yml']
}

# ==========================================
# File Info
# ==========================================

@files_bp.route('/info', methods=['POST'])
def file_info():
    """Get information about an uploaded file"""
    if 'file' not in request.files:
        return jsonify({'error': 'No file provided'}), 400
    
    file = request.files['file']
    if file.filename == '':
        return jsonify({'error': 'No file selected'}), 400
    
    filename = file.filename
    ext = os.path.splitext(filename)[1].lower()
    
    # Get file size
    file.seek(0, 2)  # Seek to end
    size = file.tell()
    file.seek(0)  # Reset to beginning
    
    # Determine category
    category = 'unknown'
    for cat, exts in SUPPORTED_TYPES.items():
        if ext in exts:
            category = cat
            break
    
    return jsonify({
        'filename': filename,
        'extension': ext,
        'size': size,
        'size_formatted': format_file_size(size),
        'category': category,
        'supported': category != 'unknown',
        'mime_type': file.content_type
    })

# ==========================================
# CSV/Spreadsheet Preview
# ==========================================

@files_bp.route('/preview/csv', methods=['POST'])
def preview_csv():
    """Preview CSV file contents"""
    if 'file' not in request.files:
        return jsonify({'error': 'No file provided'}), 400
    
    file = request.files['file']
    try:
        import csv
        content = file.read().decode('utf-8')
        reader = csv.reader(io.StringIO(content))
        
        rows = []
        headers = None
        for i, row in enumerate(reader):
            if i == 0:
                headers = row
            elif i <= 100:  # Limit preview to 100 rows
                rows.append(row)
        
        return jsonify({
            'headers': headers,
            'rows': rows,
            'total_rows': len(rows) + 1,
            'preview_limited': len(rows) == 100
        })
    except Exception as e:
        return jsonify({'error': str(e)}), 500

# ==========================================
# Text File Preview
# ==========================================

@files_bp.route('/preview/text', methods=['POST'])
def preview_text():
    """Preview text file contents"""
    if 'file' not in request.files:
        return jsonify({'error': 'No file provided'}), 400
    
    file = request.files['file']
    try:
        content = file.read().decode('utf-8')
        lines = content.split('\n')
        
        return jsonify({
            'content': content[:50000],  # Limit to 50KB
            'lines': len(lines),
            'truncated': len(content) > 50000
        })
    except Exception as e:
        return jsonify({'error': str(e)}), 500

# ==========================================
# JSON Preview
# ==========================================

@files_bp.route('/preview/json', methods=['POST'])
def preview_json():
    """Preview and validate JSON file"""
    if 'file' not in request.files:
        return jsonify({'error': 'No file provided'}), 400
    
    file = request.files['file']
    try:
        content = file.read().decode('utf-8')
        data = json.loads(content)
        
        return jsonify({
            'valid': True,
            'type': type(data).__name__,
            'keys': list(data.keys()) if isinstance(data, dict) else None,
            'length': len(data) if isinstance(data, (list, dict)) else None,
            'preview': json.dumps(data, indent=2)[:10000]  # 10KB preview
        })
    except json.JSONDecodeError as e:
        return jsonify({
            'valid': False,
            'error': str(e),
            'line': e.lineno,
            'column': e.colno
        })

# ==========================================
# Supported Types
# ==========================================

@files_bp.route('/types')
def supported_types():
    """Get list of supported file types"""
    all_extensions = []
    for exts in SUPPORTED_TYPES.values():
        all_extensions.extend(exts)
    
    return jsonify({
        'categories': SUPPORTED_TYPES,
        'all_extensions': all_extensions
    })

# ==========================================
# Helpers
# ==========================================

def format_file_size(size_bytes):
    """Format bytes to human readable size"""
    if size_bytes == 0:
        return "0 Bytes"
    
    sizes = ["Bytes", "KB", "MB", "GB", "TB"]
    import math
    i = int(math.floor(math.log(size_bytes, 1024)))
    p = math.pow(1024, i)
    s = round(size_bytes / p, 2)
    
    return f"{s} {sizes[i]}"
