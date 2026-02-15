"""
Affiliates Module - Manage affiliate links and cards
Travel, hotels, real estate, etc.
"""

from flask import Blueprint, request, jsonify
import json
import os

affiliates_bp = Blueprint('affiliates', __name__)

# ==========================================
# Affiliate Data
# ==========================================

AFFILIATES = {
    'travel': [
        {
            'id': 'airbnb',
            'name': 'Airbnb',
            'category': 'Vacation Rentals',
            'url': 'https://www.airbnb.com',
            'icon': '🏠',
            'description': 'Unique stays, homes, and experiences around the world.',
            'affiliate_url': None  # Add affiliate URL when available
        },
        {
            'id': 'vrbo',
            'name': 'VRBO',
            'category': 'Vacation Rentals',
            'url': 'https://www.vrbo.com',
            'icon': '🏡',
            'description': 'Family-friendly vacation homes and beach houses.'
        },
        {
            'id': 'booking',
            'name': 'Booking.com',
            'category': 'Hotel Search',
            'url': 'https://www.booking.com',
            'icon': '🔍',
            'description': 'Compare rates across millions of properties worldwide.'
        },
        {
            'id': 'expedia',
            'name': 'Expedia',
            'category': 'Travel Packages',
            'url': 'https://www.expedia.com',
            'icon': '✈️',
            'description': 'Flights, hotels, car rentals, and vacation packages.'
        }
    ],
    'hotels': [
        {
            'id': 'marriott',
            'name': 'Marriott Bonvoy',
            'category': 'Hotel Group',
            'url': 'https://www.marriott.com',
            'icon': '🏨',
            'description': '30+ brands including Ritz-Carlton, W Hotels, St. Regis, Westin.',
            'brands': ['Ritz-Carlton', 'St. Regis', 'W Hotels', 'Westin', 'Sheraton', 'Marriott', 'Courtyard']
        },
        {
            'id': 'hilton',
            'name': 'Hilton Honors',
            'category': 'Hotel Group',
            'url': 'https://www.hilton.com',
            'icon': '🌟',
            'description': 'Waldorf Astoria, Conrad, DoubleTree, Hampton, and more.',
            'brands': ['Waldorf Astoria', 'Conrad', 'Hilton', 'DoubleTree', 'Hampton', 'Embassy Suites']
        },
        {
            'id': 'hyatt',
            'name': 'World of Hyatt',
            'category': 'Hotel Group',
            'url': 'https://www.hyatt.com',
            'icon': '✨',
            'description': 'Park Hyatt, Andaz, Thompson Hotels, and boutique properties.',
            'brands': ['Park Hyatt', 'Andaz', 'Thompson', 'Grand Hyatt', 'Hyatt Regency']
        },
        {
            'id': 'ihg',
            'name': 'IHG One Rewards',
            'category': 'Hotel Group',
            'url': 'https://www.ihg.com',
            'icon': '🏛️',
            'description': 'InterContinental, Kimpton, Holiday Inn, and more.',
            'brands': ['InterContinental', 'Kimpton', 'Hotel Indigo', 'Crowne Plaza', 'Holiday Inn']
        }
    ],
    'real_estate': [
        {
            'id': 'zillow',
            'name': 'Zillow',
            'category': 'Home Search',
            'url': 'https://www.zillow.com',
            'icon': '🏠',
            'description': 'Browse homes for sale, Zestimates, and market data.'
        },
        {
            'id': 'redfin',
            'name': 'Redfin',
            'category': 'Home Search',
            'url': 'https://www.redfin.com',
            'icon': '🔴',
            'description': 'Tech-powered real estate with lower fees.'
        },
        {
            'id': 'realtor',
            'name': 'Realtor.com',
            'category': 'Home Search',
            'url': 'https://www.realtor.com',
            'icon': '🏡',
            'description': 'Official MLS listings, find agents, and research.'
        },
        {
            'id': 'trulia',
            'name': 'Trulia',
            'category': 'Home Search',
            'url': 'https://www.trulia.com',
            'icon': '📍',
            'description': 'Neighborhood insights, crime maps, schools.'
        },
        {
            'id': 'compass',
            'name': 'Compass',
            'category': 'Luxury Real Estate',
            'url': 'https://www.compass.com',
            'icon': '🧭',
            'description': 'Premium listings and tech-forward agents.'
        },
        {
            'id': 'sothebys',
            'name': "Sotheby's Realty",
            'category': 'Luxury Real Estate',
            'url': 'https://www.sothebysrealty.com',
            'icon': '💎',
            'description': 'Extraordinary properties worldwide.'
        }
    ]
}

# ==========================================
# API Endpoints
# ==========================================

@affiliates_bp.route('/all')
def get_all_affiliates():
    """Get all affiliate cards"""
    return jsonify(AFFILIATES)

@affiliates_bp.route('/category/<category>')
def get_category(category):
    """Get affiliates by category"""
    if category not in AFFILIATES:
        return jsonify({'error': f'Category {category} not found'}), 404
    return jsonify({
        'category': category,
        'affiliates': AFFILIATES[category]
    })

@affiliates_bp.route('/travel')
def get_travel():
    """Get travel affiliates (Airbnb, VRBO, etc.)"""
    return jsonify({
        'category': 'travel',
        'affiliates': AFFILIATES['travel']
    })

@affiliates_bp.route('/hotels')
def get_hotels():
    """Get hotel group affiliates"""
    return jsonify({
        'category': 'hotels',
        'affiliates': AFFILIATES['hotels']
    })

@affiliates_bp.route('/real-estate')
def get_real_estate():
    """Get real estate affiliates"""
    return jsonify({
        'category': 'real_estate',
        'affiliates': AFFILIATES['real_estate']
    })

@affiliates_bp.route('/search')
def search_affiliates():
    """Search across all affiliates"""
    query = request.args.get('q', '').lower()
    
    if not query:
        return jsonify({'error': 'Query required'}), 400
    
    results = []
    for category, affiliates in AFFILIATES.items():
        for aff in affiliates:
            # Search in name, category, description
            searchable = f"{aff['name']} {aff['category']} {aff['description']}".lower()
            if query in searchable:
                results.append({**aff, 'source_category': category})
    
    return jsonify({
        'query': query,
        'count': len(results),
        'results': results
    })

@affiliates_bp.route('/link/<affiliate_id>')
def get_affiliate_link(affiliate_id):
    """Get specific affiliate link"""
    for category, affiliates in AFFILIATES.items():
        for aff in affiliates:
            if aff['id'] == affiliate_id:
                return jsonify({
                    'affiliate': aff,
                    'url': aff.get('affiliate_url') or aff['url']
                })
    
    return jsonify({'error': 'Affiliate not found'}), 404
