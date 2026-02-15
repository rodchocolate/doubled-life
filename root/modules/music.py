"""
Music Module - YouTube, Spotify, SoundCloud integrations
Non-commercial music/media focus
"""

from flask import Blueprint, request, jsonify
import os

music_bp = Blueprint('music', __name__)

# ==========================================
# YouTube Integration (Public API)
# ==========================================

YOUTUBE_API_KEY = os.environ.get('YOUTUBE_API_KEY', '')

@music_bp.route('/youtube/search')
def youtube_search():
    """Search YouTube videos"""
    query = request.args.get('q', '')
    max_results = request.args.get('max', 10, type=int)
    
    if not query:
        return jsonify({'error': 'Query required'}), 400
    
    # For now, return search URL - full API integration optional
    return jsonify({
        'query': query,
        'search_url': f'https://www.youtube.com/results?search_query={query}',
        'note': 'Direct API integration available with YOUTUBE_API_KEY'
    })

@music_bp.route('/youtube/embed/<video_id>')
def youtube_embed(video_id):
    """Get YouTube embed URL"""
    return jsonify({
        'video_id': video_id,
        'embed_url': f'https://www.youtube.com/embed/{video_id}',
        'watch_url': f'https://www.youtube.com/watch?v={video_id}'
    })

# ==========================================
# Spotify Integration
# ==========================================

@music_bp.route('/spotify/search')
def spotify_search():
    """Search Spotify (link generator)"""
    query = request.args.get('q', '')
    search_type = request.args.get('type', 'track')  # track, album, artist, playlist
    
    if not query:
        return jsonify({'error': 'Query required'}), 400
    
    return jsonify({
        'query': query,
        'type': search_type,
        'spotify_url': f'https://open.spotify.com/search/{query}',
        'uri': f'spotify:search:{query}'
    })

# ==========================================
# SoundCloud Integration
# ==========================================

@music_bp.route('/soundcloud/search')
def soundcloud_search():
    """Search SoundCloud"""
    query = request.args.get('q', '')
    
    if not query:
        return jsonify({'error': 'Query required'}), 400
    
    return jsonify({
        'query': query,
        'search_url': f'https://soundcloud.com/search?q={query}'
    })

# ==========================================
# Generic Music Links
# ==========================================

@music_bp.route('/links')
def music_links():
    """Get curated music platform links"""
    return jsonify({
        'platforms': [
            {
                'name': 'YouTube',
                'url': 'https://youtube.com',
                'icon': 'fab fa-youtube',
                'category': 'video'
            },
            {
                'name': 'Spotify',
                'url': 'https://spotify.com',
                'icon': 'fab fa-spotify',
                'category': 'streaming'
            },
            {
                'name': 'SoundCloud',
                'url': 'https://soundcloud.com',
                'icon': 'fab fa-soundcloud',
                'category': 'independent'
            },
            {
                'name': 'Bandcamp',
                'url': 'https://bandcamp.com',
                'icon': 'fab fa-bandcamp',
                'category': 'independent'
            },
            {
                'name': 'Apple Music',
                'url': 'https://music.apple.com',
                'icon': 'fab fa-apple',
                'category': 'streaming'
            },
            {
                'name': 'Mixcloud',
                'url': 'https://mixcloud.com',
                'icon': 'fas fa-headphones',
                'category': 'mixes'
            }
        ]
    })
