#!/usr/bin/env python3
"""
DOUBLED.LIFE - Simple Flask Server
Serves static files and provides basic API endpoints

Run: python server.py
"""

from flask import Flask, send_from_directory, jsonify, request
from flask_cors import CORS
import os
import json

app = Flask(__name__)
app.secret_key = os.environ.get('FLASK_SECRET', 'doubled-life-dev')
CORS(app, supports_credentials=True)

# ==========================================
# Static File Serving
# ==========================================

@app.route('/')
def index():
    return send_from_directory('.', 'index.html')

@app.route('/<path:filename>')
def serve_file(filename):
    if os.path.isfile(filename):
        return send_from_directory('.', filename)
    return 'Not found', 404

@app.route('/backgrounds/<path:filename>')
def serve_backgrounds(filename):
    return send_from_directory('backgrounds', filename)

# ==========================================
# API Endpoints
# ==========================================

@app.route('/api/health')
def health():
    return jsonify({
        'status': 'ok',
        'service': 'doubled.life'
    })

@app.route('/api/mortgage/calculate', methods=['POST'])
def calculate_mortgage():
    """Calculate mortgage payment"""
    data = request.get_json() or {}
    
    home_price = float(data.get('home_price', 400000))
    down_payment = float(data.get('down_payment', 80000))
    interest_rate = float(data.get('interest_rate', 6.5))
    loan_term = int(data.get('loan_term', 30))
    
    loan_amount = home_price - down_payment
    monthly_rate = (interest_rate / 100) / 12
    num_payments = loan_term * 12
    
    if monthly_rate == 0:
        monthly_payment = loan_amount / num_payments
    else:
        monthly_payment = loan_amount * (monthly_rate * pow(1 + monthly_rate, num_payments)) / \
                         (pow(1 + monthly_rate, num_payments) - 1)
    
    total_paid = monthly_payment * num_payments
    total_interest = total_paid - loan_amount
    
    return jsonify({
        'monthly_payment': round(monthly_payment, 2),
        'loan_amount': loan_amount,
        'total_interest': round(total_interest, 2),
        'total_paid': round(total_paid, 2)
    })

@app.route('/api/affiliates')
def get_affiliates():
    """Get affiliate cards"""
    affiliates = {
        'travel': [
            {'id': 'airbnb', 'name': 'Airbnb', 'url': 'https://www.airbnb.com', 'icon': '🏠', 'category': 'Vacation Rentals'},
            {'id': 'vrbo', 'name': 'VRBO', 'url': 'https://www.vrbo.com', 'icon': '🏡', 'category': 'Vacation Rentals'},
            {'id': 'booking', 'name': 'Booking.com', 'url': 'https://www.booking.com', 'icon': '🔍', 'category': 'Hotel Search'}
        ],
        'hotels': [
            {'id': 'marriott', 'name': 'Marriott Bonvoy', 'url': 'https://www.marriott.com', 'icon': '🏨', 'category': 'Hotel Group'},
            {'id': 'hilton', 'name': 'Hilton Honors', 'url': 'https://www.hilton.com', 'icon': '🌟', 'category': 'Hotel Group'},
            {'id': 'hyatt', 'name': 'World of Hyatt', 'url': 'https://www.hyatt.com', 'icon': '✨', 'category': 'Hotel Group'}
        ],
        'real_estate': [
            {'id': 'zillow', 'name': 'Zillow', 'url': 'https://www.zillow.com', 'icon': '🏠', 'category': 'Home Search'},
            {'id': 'redfin', 'name': 'Redfin', 'url': 'https://www.redfin.com', 'icon': '🔴', 'category': 'Home Search'},
            {'id': 'realtor', 'name': 'Realtor.com', 'url': 'https://www.realtor.com', 'icon': '🏡', 'category': 'Home Search'}
        ]
    }
    
    category = request.args.get('category')
    if category and category in affiliates:
        return jsonify({category: affiliates[category]})
    
    return jsonify(affiliates)

# ==========================================
# Run Server
# ==========================================

if __name__ == '__main__':
    port = int(os.environ.get('PORT', 8080))
    
    print(f"""
╔════════════════════════════════════════════╗
║         DOUBLED.LIFE                       ║
║     http://localhost:{port}                  ║
╚════════════════════════════════════════════╝
    """)
    
    app.run(host='0.0.0.0', port=port, debug=True)
