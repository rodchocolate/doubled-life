"""
Tools Module - Calculators and utilities
Mortgage calculator, converters, etc.
"""

from flask import Blueprint, request, jsonify

tools_bp = Blueprint('tools', __name__)

# ==========================================
# Mortgage Calculator
# ==========================================

@tools_bp.route('/mortgage/calculate', methods=['POST'])
def calculate_mortgage():
    """Calculate monthly mortgage payment"""
    data = request.get_json() or {}
    
    home_price = float(data.get('home_price', 400000))
    down_payment = float(data.get('down_payment', 80000))
    interest_rate = float(data.get('interest_rate', 6.5))
    loan_term = int(data.get('loan_term', 30))
    
    # Property tax and insurance (annual, as percentage of home value)
    property_tax_rate = float(data.get('property_tax_rate', 1.2))  # 1.2% default
    insurance_rate = float(data.get('insurance_rate', 0.5))  # 0.5% default
    pmi_rate = float(data.get('pmi_rate', 0.5))  # PMI if < 20% down
    
    loan_amount = home_price - down_payment
    down_payment_percent = (down_payment / home_price) * 100
    
    # Monthly interest rate
    monthly_rate = (interest_rate / 100) / 12
    num_payments = loan_term * 12
    
    # Calculate principal + interest payment
    if monthly_rate == 0:
        monthly_pi = loan_amount / num_payments
    else:
        monthly_pi = loan_amount * (monthly_rate * pow(1 + monthly_rate, num_payments)) / \
                    (pow(1 + monthly_rate, num_payments) - 1)
    
    # Monthly property tax
    monthly_tax = (home_price * property_tax_rate / 100) / 12
    
    # Monthly insurance
    monthly_insurance = (home_price * insurance_rate / 100) / 12
    
    # PMI (if down payment < 20%)
    monthly_pmi = 0
    if down_payment_percent < 20:
        monthly_pmi = (loan_amount * pmi_rate / 100) / 12
    
    # Total monthly payment
    total_monthly = monthly_pi + monthly_tax + monthly_insurance + monthly_pmi
    
    # Total over life of loan
    total_paid = monthly_pi * num_payments
    total_interest = total_paid - loan_amount
    
    return jsonify({
        'monthly_payment': round(total_monthly, 2),
        'breakdown': {
            'principal_interest': round(monthly_pi, 2),
            'property_tax': round(monthly_tax, 2),
            'insurance': round(monthly_insurance, 2),
            'pmi': round(monthly_pmi, 2)
        },
        'loan_details': {
            'loan_amount': loan_amount,
            'down_payment_percent': round(down_payment_percent, 1),
            'interest_rate': interest_rate,
            'loan_term_years': loan_term,
            'total_payments': num_payments
        },
        'totals': {
            'total_principal_interest': round(total_paid, 2),
            'total_interest': round(total_interest, 2),
            'total_cost': round(total_paid + (monthly_tax + monthly_insurance + monthly_pmi) * num_payments, 2)
        }
    })

# ==========================================
# Amortization Schedule
# ==========================================

@tools_bp.route('/mortgage/amortization', methods=['POST'])
def amortization_schedule():
    """Generate amortization schedule"""
    data = request.get_json() or {}
    
    loan_amount = float(data.get('loan_amount', 320000))
    interest_rate = float(data.get('interest_rate', 6.5))
    loan_term = int(data.get('loan_term', 30))
    
    monthly_rate = (interest_rate / 100) / 12
    num_payments = loan_term * 12
    
    if monthly_rate == 0:
        monthly_payment = loan_amount / num_payments
    else:
        monthly_payment = loan_amount * (monthly_rate * pow(1 + monthly_rate, num_payments)) / \
                         (pow(1 + monthly_rate, num_payments) - 1)
    
    schedule = []
    balance = loan_amount
    total_interest = 0
    total_principal = 0
    
    for month in range(1, num_payments + 1):
        interest_payment = balance * monthly_rate
        principal_payment = monthly_payment - interest_payment
        balance -= principal_payment
        
        total_interest += interest_payment
        total_principal += principal_payment
        
        # Only include yearly summaries for brevity
        if month % 12 == 0 or month == 1:
            schedule.append({
                'month': month,
                'year': (month - 1) // 12 + 1,
                'payment': round(monthly_payment, 2),
                'principal': round(principal_payment, 2),
                'interest': round(interest_payment, 2),
                'balance': max(0, round(balance, 2)),
                'total_interest_paid': round(total_interest, 2),
                'total_principal_paid': round(total_principal, 2)
            })
    
    return jsonify({
        'monthly_payment': round(monthly_payment, 2),
        'schedule': schedule,
        'summary': {
            'total_payments': num_payments,
            'total_paid': round(monthly_payment * num_payments, 2),
            'total_interest': round(total_interest, 2),
            'total_principal': round(total_principal, 2)
        }
    })

# ==========================================
# Currency Converter (placeholder)
# ==========================================

@tools_bp.route('/convert/currency')
def convert_currency():
    """Currency conversion (would need API key for live rates)"""
    amount = request.args.get('amount', 1, type=float)
    from_currency = request.args.get('from', 'USD').upper()
    to_currency = request.args.get('to', 'EUR').upper()
    
    # Placeholder rates - would use live API in production
    rates = {
        'USD': 1.0,
        'EUR': 0.92,
        'GBP': 0.79,
        'JPY': 149.50,
        'CAD': 1.36,
        'AUD': 1.53,
        'CHF': 0.88
    }
    
    if from_currency not in rates or to_currency not in rates:
        return jsonify({'error': 'Unsupported currency'}), 400
    
    # Convert through USD
    in_usd = amount / rates[from_currency]
    result = in_usd * rates[to_currency]
    
    return jsonify({
        'amount': amount,
        'from': from_currency,
        'to': to_currency,
        'result': round(result, 2),
        'rate': round(rates[to_currency] / rates[from_currency], 4),
        'note': 'Rates are approximate. Use live API for accuracy.'
    })

# ==========================================
# Unit Converter
# ==========================================

@tools_bp.route('/convert/units')
def convert_units():
    """Convert between common units"""
    value = request.args.get('value', 1, type=float)
    from_unit = request.args.get('from', 'mi').lower()
    to_unit = request.args.get('to', 'km').lower()
    
    # Conversion factors to base units
    conversions = {
        # Length (base: meters)
        'mi': 1609.344,
        'km': 1000,
        'm': 1,
        'ft': 0.3048,
        'in': 0.0254,
        'yd': 0.9144,
        # Weight (base: grams)
        'lb': 453.592,
        'kg': 1000,
        'g': 1,
        'oz': 28.3495,
        # Temperature handled separately
    }
    
    if from_unit not in conversions or to_unit not in conversions:
        return jsonify({'error': 'Unsupported unit'}), 400
    
    # Convert to base, then to target
    base_value = value * conversions[from_unit]
    result = base_value / conversions[to_unit]
    
    return jsonify({
        'value': value,
        'from': from_unit,
        'to': to_unit,
        'result': round(result, 4)
    })
