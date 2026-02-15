# DOUBLED.LIFE

> *"my father had a rolodex at the office. we had an address book at home. simpler. less efficient? more personal."*

**Home Workspace** — A personal digital hub.

## Current Status

**What actually works right now:**

| Feature | Status | Notes |
|---------|--------|-------|
| Landing page | ✅ Works | `index.html` with tagline, click to enter |
| Main hub | ✅ Works | `main.html` with all sections |
| YouTube search | ⚠️ Partial | Opens YouTube link (no API key set) |
| Music search history | ✅ Works | localStorage, separate from general |
| Music service links | ✅ Works | Spotify, SoundCloud, Bandcamp |
| File viewer | ✅ Works | PDF, images, text, CSV preview |
| Mortgage calculator | ✅ Works | Client-side JS |
| Affiliate cards | ✅ Works | Travel, hotels, real estate |
| Flask server | ✅ Works | Static files + mortgage API |
| **Backgrounds** | ❌ Missing | Folder empty, need to copy from toxico |

**Not wired up yet:**
- YouTube API (needs key in `main.html`)
- Modular server (`root/` folder) — structure exists but modules are placeholders
- LLM integration — no AI features yet

### Fix Backgrounds

```bash
# Copy backgrounds from toxico
cp /Users/jasonjenkins/Desktop/alpha/toxico/bg-graffiti-*.jpg /Users/jasonjenkins/Desktop/alpha/doubled.life/backgrounds/
```

## Quick Start

```bash
# Install dependencies
pip install -r requirements.txt

# Run server
python server.py
```

Open http://localhost:8080

## Structure

```
doubled.life/
├── index.html          # Landing page
├── main.html           # Main hub with all features
├── styles.css          # Base styles
├── server.py           # Simple Flask server
├── requirements.txt    # Python dependencies
├── backgrounds/        # Background images (from toxico)
└── root/               # Modular server for GoDaddy hosting
    ├── app.py          # Main Flask app
    └── modules/        # Modular API endpoints
        ├── music.py    # YouTube, Spotify, SoundCloud
        ├── files.py    # File handling
        ├── tools.py    # Calculators
        └── affiliates.py # Affiliate links
```

## Features

### YouTube Search & Embeds
- 3 full-width embedded video results
- **Music searches tracked separately** for deeper exploration
- General/How-To/Music tabs
- Search history for both categories
- Works without API key (opens YouTube), better with API key (embedded results)

### Music Services
- Spotify, SoundCloud, Bandcamp links
- Non-commercial, discovery-focused

### File Viewer
- PDF preview (iframe embed)
- CSV/text file viewing
- Image preview
- XLSX/DOCX indicators

### Mortgage Calculator
- Monthly payment calculation
- Principal, interest, total breakdown

### Affiliate Cards
- **Travel**: Airbnb, VRBO, Booking.com
- **Hotels**: Marriott Bonvoy, Hilton Honors, World of Hyatt
- **Real Estate**: Zillow, Redfin, Realtor.com, Compass, Sotheby's

## Adding YouTube API Key

For full embedded search results, add your YouTube Data API key:

1. Get a key from [Google Cloud Console](https://console.cloud.google.com/)
2. Enable YouTube Data API v3
3. Edit `main.html` and set: `const YOUTUBE_API_KEY = 'your-key-here';`

## GoDaddy Deployment

Use the `root/` folder structure for modular deployment:

1. Upload `root/` contents to your hosting
2. Configure Python/CGI
3. Set `FLASK_SECRET` environment variable
4. Point domain to `app.py`

## Philosophy

This is a **home workspace** — not a corporate tool. 

- Personal, not professional
- Simpler, even if less "efficient"
- A place to keep useful things together
- Like the family address book, not the office Rolodex

### 🏠 Home Sandbox

> **For people who understand MD/MCP leverage.**

You know that:
- Markdown is the universal interchange format
- JSON is structured thought
- MCP (Model Context Protocol) turns local files into AI superpowers
- The file system is the database
- Git is memory

This is your sandbox. Not a SaaS product. Not a walled garden. Just files you control, structured for LLM consumption when you need it.

### 🧠 No Persistent Sessions

> **Core principle:** Clean, clear, uncluttered LLM every time you sit down.

The LLM is a **Markov chain to result** — not a memory bank. It processes, it produces, it's done.

**Only artifacts are:**
- `.md` — Human-readable documentation, plans, notes
- `.json` — Structured data, lists, configurations

**No:**
- Chat history accumulation
- "Remember when we talked about..."
- Context bloat across sessions
- Personality drift from extended conversations

**Why this matters:**
- Fresh perspective every session
- No hallucinated "memories"
- Artifacts are the truth, not the conversation
- You own the data in files, not trapped in chat logs
- Reproducible — same inputs → same outputs

**Workflow:**
1. Sit down with a task
2. Feed LLM the relevant `.md` / `.json` context
3. LLM processes → produces result
4. Save artifact (`.md` or `.json`)
5. Session ends clean

The address book stays. The conversation doesn't.

### 🤖 Default LLM: Groq

> **Groq is the default.** Fast, free tier, no persistent state.

**Why Groq:**
- Fastest inference (LPU, not GPU)
- Free tier: 30 req/min on Llama 3, Mixtral
- Simple API (OpenAI-compatible)
- No chat history stored server-side
- Perfect for Markov-chain-to-result workflow

**Groq Setup:**
```bash
# Get API key from https://console.groq.com
export GROQ_API_KEY="gsk_..."
```

**Example call:**
```python
from groq import Groq

client = Groq()
response = client.chat.completions.create(
    model="llama-3.1-8b-instant",
    messages=[{"role": "user", "content": "Summarize this: ..."}]
)
print(response.choices[0].message.content)
```

**Fallback options:**
| Provider | Model | Use Case |
|----------|-------|----------|
| **Groq** (default) | llama-3.1-8b-instant | Fast, daily tasks |
| Groq | llama-3.1-70b | Deeper reasoning |
| Groq | mixtral-8x7b | Code, structured output |
| OpenAI | gpt-4o-mini | When you need OpenAI compat |
| Anthropic | claude-3-haiku | Quick Claude tasks |
| Local | ollama | Offline, private |

---

## Future Additions Roadmap

### 📺 Media Sources

| Source | What It Does | API/Integration |
|--------|--------------|-----------------|
| **Reddit** | Subreddit feeds, saved posts, gift idea threads (r/BuyItForLife, r/GiftIdeas) | Reddit API (OAuth) or RSS feeds |
| **X/Twitter** | Bookmarks, lists, saved tweets | Twitter API v2 (requires dev account) |
| **Pinterest** | Boards, pins, visual inspiration | Pinterest API |
| **Pocket** | Read-later articles, tags | Pocket API |
| **Instapaper** | Saved articles | Instapaper API |
| **Feedly/RSS** | Custom news feeds | Feedly API or raw RSS |
| **TikTok** | Saved/favorited videos | Limited API, mostly embed-only |
| **Vimeo** | Alternative video embeds | Vimeo oEmbed |
| **Twitch** | Followed channels, VODs | Twitch API |
| **Podcasts** | Apple Podcasts, Spotify shows | iTunes Search API, Spotify API |

### 🎧 Curated Music Sources (for toxico / melodiclabs)

> **Note:** These are curated, tastemaker-driven sources — perfect for music discovery beyond algorithmic recommendations.

| Source | What It Does | Integration |
|--------|--------------|-------------|
| **Resident Advisor** | DJ charts, reviews, event listings, RA Exchange podcast | RSS, scraping, or unofficial API |
| **Discogs** | Record collection, wantlist, marketplace | Discogs API |
| **Bandcamp** | Daily editorial, genre tags, artist pages | Bandcamp API (limited) + scraping |
| **Pitchfork** | Reviews, Best New Music, features | RSS feeds |
| **The Quietus** | Underground/experimental reviews | RSS |
| **Mixcloud** | DJ mixes, radio shows | Mixcloud API |
| **NTS Radio** | Live streams, show archive | NTS API/embed |
| **Rinse FM** | UK underground radio | Stream embed |
| **The Lot Radio** | Brooklyn community radio | Stream embed |
| **KEXP** | Live sessions, curated playlists | YouTube/RSS |
| **Boiler Room** | Live DJ sets, archive | YouTube API |
| **HÖR Berlin** | Streaming DJ sets | YouTube API |
| **Crack Magazine** | Editorial, mixes, features | RSS |
| **XLR8R** | Mixes, reviews, downloads | RSS |
| **Tiny Mix Tapes** | Experimental music coverage | RSS (archived) |
| **Rate Your Music** | User ratings, lists, recommendations | Scraping only |
| **Album of the Year** | Aggregate scores, user lists | Scraping |
| **1001 Albums Generator** | Daily album discovery challenge | API available |

### 🎁 Gift & List Tools

| Tool | What It Does | Integration |
|------|--------------|-------------|
| **Christmas List Maker** | Family gift tracking, budgets, purchased status | localStorage + optional sharing |
| **Birthday Tracker** | Annual reminders, gift history | Calendar sync |
| **Wishlist Aggregator** | Pull from Amazon, Etsy, any URL | URL scraping + oEmbed |
| **Gift Budget Tracker** | Per-person spending limits | Simple JS calculator |
| **Secret Santa Generator** | Random assignments, exclusions | Local algorithm |

### 📅 Calendar & Planning

| Tool | What It Does | Integration |
|------|--------------|-------------|
| **Google Calendar** | View/add events, vacation blocks | Google Calendar API |
| **Apple Calendar** | iCloud calendar sync | CalDAV or iCloud API |
| **Vacation Planner** | Trip itinerary builder | Google Calendar + Maps |
| **Countdown Timer** | Days until Christmas, vacation, etc. | Simple JS |
| **Weekly Meal Planner** | Drag-drop meals, grocery list | localStorage |
| **Chore Schedule** | Rotating household tasks | Calendar integration |

### 🔌 Google Integrations

| Service | What It Does | API |
|---------|--------------|-----|
| **Google Drive** | File browser, recent docs | Drive API |
| **Google Photos** | Album viewer, memories | Photos API |
| **Google Keep** | Notes, lists, reminders | Keep API (limited) |
| **Google Tasks** | To-do lists | Tasks API |
| **Google Maps** | Saved places, directions | Maps Embed API |
| **Gmail** | Quick compose, label counts | Gmail API |
| **Google Contacts** | Address book (full circle!) | People API |

### 🛠️ Home Tools

| Tool | What It Does | Complexity |
|------|--------------|------------|
| **Unit Converter** | Cooking, measurements, currency | Simple JS |
| **Tip Calculator** | Split bills, custom tip % | Simple JS |
| **Loan Comparison** | Compare mortgage offers side-by-side | Medium JS |
| **Home Inventory** | Track valuables for insurance | localStorage + export |
| **Password Generator** | Secure random passwords | Simple JS |
| **QR Code Generator** | WiFi, URLs, contact cards | JS library |
| **Timer/Stopwatch** | Cooking, workouts | Simple JS |

### 🏠 Home Services (Affiliate Opportunities)

| Category | Services |
|----------|----------|
| **Insurance** | Lemonade, Geico, Progressive, State Farm |
| **Utilities** | Compare energy rates, internet providers |
| **Home Repair** | Thumbtack, Angi, HomeAdvisor |
| **Lawn & Garden** | Sunday, TruGreen |
| **Cleaning** | Handy, Molly Maid |
| **Moving** | PODS, U-Haul, Two Men and a Truck |
| **Storage** | Public Storage, Extra Space |
| **Pet Care** | Rover, Wag, Chewy |

### 📊 Personal Finance

| Tool | What It Does | Integration |
|------|--------------|-------------|
| **Budget Tracker** | Monthly income/expenses | localStorage |
| **Subscription Manager** | Track recurring charges | Manual entry |
| **Net Worth Calculator** | Assets minus liabilities | Simple form |
| **Investment Lookup** | Stock/crypto prices | Yahoo Finance API |
| **Bill Reminder** | Due date alerts | Calendar sync |

### 🎯 Actions & Automations

| Action | What It Does | How |
|--------|--------------|-----|
| **Quick Share** | Share to family group chat | Web Share API |
| **Print View** | Clean printable version | CSS @media print |
| **Export to PDF** | Save lists, plans | jsPDF library |
| **Email Summary** | Weekly digest of lists | Backend email service |
| **Voice Input** | Speak to add items | Web Speech API |
| **Scan Barcode** | Add products to lists | Camera + barcode.js |

### 🔗 One-Click Launchers

Quick-launch buttons for family essentials:
- **School Portal** — saved login link
- **Doctor/Dentist** — appointment booking
- **Pharmacy** — refill orders
- **Library** — catalog search, account
- **Local Weather** — weather.gov embed
- **Traffic/Commute** — Google Maps traffic
- **Streaming** — Netflix, Disney+, etc. profiles
- **Family Group Chat** — iMessage, WhatsApp web

### 💡 Implementation Priority

**Phase 1 — Low-Hanging Fruit (JS only)**
- Christmas list maker
- Countdown timers
- Unit/tip calculators
- QR code generator
- Wishlist from URL

**Phase 2 — Google Integration**
- Calendar view
- Drive file browser
- Contacts sync

**Phase 3 — Social Media**
- Reddit saved posts
- X/Twitter bookmarks
- Pinterest boards

**Phase 4 — Advanced**
- Voice input
- Barcode scanning
- Email digests
- Cross-device sync

---

## 📱 Phone-Native Features (Later Consideration)

> **Note:** These are things people already do on their phones daily. Tag for later — either we naturally include them because other features depend on them, or users will expect them in a complete home ecosystem.

### Core Phone Functions
| Feature | Why It Matters | Dependency/Ecosystem Need |
|---------|---------------|---------------------------|
| 📞 **Contacts** | Address book is literally the tagline | Google/Apple Contacts API for sharing, invites |
| 📅 **Calendar** | Vacation planning, reminders, countdowns | Google/Apple Calendar for scheduling features |
| 📷 **Camera** | Barcode scanning, receipt capture, document scan | WebRTC for barcode/QR features |
| 🎤 **Voice Memo** | Quick notes, grocery list dictation | Web Speech API |
| ⏰ **Alarms/Timers** | Cooking, laundry, medications | Notification API |
| 📍 **Location** | Weather, traffic, nearby services | Geolocation API |
| 🔔 **Notifications** | Bill reminders, shipping updates, calendar alerts | Push API / Service Workers |

### Communication (People Already Have Apps)
| Feature | Phone App | Ecosystem Consideration |
|---------|-----------|------------------------|
| 📧 **Email** | Gmail, Apple Mail | Quick compose, inbox count badge |
| 💬 **Messaging** | iMessage, WhatsApp, Signal | Share buttons, family group links |
| 📹 **Video Calls** | FaceTime, Zoom, Meet | One-click launch links only |
| 📱 **Phone Calls** | Native dialer | `tel:` links for doctor, pharmacy |

### Media Consumption (Heavy Phone Use)
| Feature | Phone App | Ecosystem Consideration |
|---------|-----------|------------------------|
| 🎵 **Music** | Spotify, Apple Music | Already core to doubled.life |
| 📺 **Video** | YouTube, Netflix, etc. | YouTube embeds done, streaming launchers |
| 📚 **Reading** | Kindle, News apps | Pocket/Instapaper integration |
| 🎙️ **Podcasts** | Apple/Spotify Podcasts | Feed integration, now playing |
| 📸 **Photos** | Camera roll, Google Photos | Gallery view, memory sharing |

### Shopping & Commerce (Daily Phone Use)
| Feature | Phone App | Ecosystem Consideration |
|---------|-----------|------------------------|
| 🛒 **Shopping Lists** | Notes, Reminders, AnyList | Core grocery/gift list feature |
| 📦 **Package Tracking** | Shop, individual carrier apps | Aggregate tracking widget |
| 💳 **Payments** | Apple Pay, Venmo, PayPal | Links only, no actual processing |
| 🏷️ **Coupons/Deals** | Honey, RetailMeNot | Browser extension territory |
| 🛍️ **Wishlists** | Amazon, individual stores | Aggregate wishlist (planned) |

### Utilities (Phone-First)
| Feature | Phone App | Ecosystem Consideration |
|---------|-----------|------------------------|
| 🌤️ **Weather** | Weather app | Widget/embed, location-based |
| 🗺️ **Maps/Navigation** | Google/Apple Maps | Saved places, commute, traffic |
| 🔦 **Flashlight** | Native | N/A (hardware) |
| 📐 **Calculator** | Native | Already building converters |
| 🔐 **Passwords** | 1Password, Keychain | Generator only, no vault |
| 📊 **Health/Fitness** | Apple Health, Fitbit | Data too personal, skip |
| 💰 **Banking** | Bank apps | Links only, never actual banking |

### Smart Home (Growing Phone Use)
| Feature | Phone App | Ecosystem Consideration |
|---------|-----------|------------------------|
| 💡 **Lights** | Hue, HomeKit | API possible but complex |
| 🌡️ **Thermostat** | Nest, Ecobee | Same — links only |
| 🔒 **Locks/Security** | Ring, SimpliSafe | Links only |
| 📺 **TV Control** | Roku, Apple TV | Deep link launchers |

### 🎯 Phone Feature Strategy

**Include (features depend on it):**
- Contacts → for sharing, family lists
- Calendar → for planning features
- Camera → for barcode scanning
- Location → for weather, nearby
- Notifications → for reminders

**Link Only (people have preferred apps):**
- Email, messaging, video calls
- Banking, payments
- Smart home controls
- Health/fitness data

**Build Our Version (home workspace angle):**
- Lists (grocery, gifts, chores)
- Media aggregation (YouTube, music sources)
- File viewing
- Calculators/tools
- Affiliate discovery

**Skip Entirely:**
- Anything requiring sensitive data storage
- Anything phones do better natively (flashlight, compass)
- Social media posting (consumption only)

---

## 📋 PUNCHLIST

### Personalities
> Different AI personas for different pages/contexts

| Name | Domain | Vibe |
|------|--------|------|
| **toxico** | Fashion, style, luxury goods | Confident tastemaker, knows what's cool |
| **liberates** | Freedom, travel, experiences | Adventurous, spontaneous |
| **markov** | Logic, systems, technical | Precise, methodical, Markov-chain thinking |
| **laplace** | Probability, predictions, analysis | Statistical, probabilistic reasoning |
| **carmen** | Romance, passion, lifestyle | Warm, sensual, curated taste |
| **nina** | Music, underground, culture | Deep knowledge, tastemaker energy |
| **katie** | Home, family, practical | Friendly, organized, helpful |

### Content Categories (Video Library)

**Visuals as Background + Audio Matters (DJ Sets)**
> Music-first content where you can work/vibe with it on

| Category | Sources | Count |
|----------|---------|-------|
| Cercle | @Cercle | 19 |
| Boiler Room | @boilerroom | 7 |
| Defected | @DefectedMusic | 3 |
| Drumcode/CamelPhat | Drumcode | 4 |
| Pacha Ibiza | @pacha | 7 |
| Mixmag Lab | @Mixmag | 5 |
| Festival Sets | @LucaDeaOfficial | 10 |
| Celebrity DJs | Various | 3 |

**Visuals Matter, Sound Optional (Action/Sports)**
> Watch with sound off, pure visual spectacle

| Category | Sources | Status |
|----------|---------|--------|
| Ski Films | @MatchstickProductions | ✅ 10 videos |
| Surf Films | TBD | 🔜 Scrape |
| Classic Games | ESPN Classic, NFL Films | 🔜 Scrape |
| Highlight Reels | Team channels | 🔜 Scrape |

**Comedy/Entertainment**
> Full attention content

| Category | Sources | Status |
|----------|---------|--------|
| Stand-up Specials | Netflix Is A Joke, etc | 🔜 |
| Late Night Clips | Conan, Fallon, etc | 🔜 |
| Classic Comedy | SNL, etc | 🔜 |

### Sports Streaming - BYOP (Bring Your Own Password)
> Add to settings modal - user provides their own credentials

| Service | Free Option? | Notes |
|---------|--------------|-------|
| **ESPN+** | ❌ Sub required | Lots of content |
| **YouTube TV** | ❌ Sub required | Live sports |
| **Peacock** | ⚠️ Limited free | Some Premier League |
| **Paramount+** | ⚠️ Limited free | Champions League |
| **NFHS Network** | ✅ FREE | High school sports! |
| **NCAA March Madness** | ✅ FREE in March | Tournament games |
| **Pluto TV** | ✅ FREE | Some sports channels |
| **Tubi** | ✅ FREE | Classic games/docs |
| **ESPN YouTube** | ✅ FREE | Highlights, docs |
| **NFL YouTube** | ✅ FREE | Highlights |
| **NBA YouTube** | ✅ FREE | Highlights |

### Free Sports Channels to Scrape
```
@espn - highlights, 30 for 30 docs
@NFL - classic games, highlights  
@NBA - highlights, classic games
@NHL - highlights
@MLB - classic games
@nfaborhood - behind the scenes
@NFLThrowback - classic content
```

---

## 🛠️ CAPABILITIES (Ask in Chat)

> **No hub needed** — just ask what it can do

### Video Discovery & Curation
| Capability | How | Notes |
|------------|-----|-------|
| **YouTube Search** | Chat query → LLM interprets → YouTube results | Gemini/Groq powered |
| **Curated Library** | Pre-loaded best picks, keyword matching | 100+ videos, growing |
| **yt-dlp Scraping** | Bulk scrape channels/playlists for video IDs | Agent-assisted |
| **Category Browse** | "ski films", "DJ sets", "candid interviews" | Keyword → curated matches |

### Current Curated Categories
- DJ Sets (Cercle, Boiler Room, Defected, Pacha, Mixmag, Drumcode)
- Ski/Action Films (Matchstick Productions)
- Sports Documentaries (NFL Films, ESPN)
- Candid Athlete Interviews (Ricky Williams, Iverson, Barkley, Tyson, Marshawn)
- Human Interest Docs (Manute Bol, Sisqo in Suburbs)
- Sports Illustrated Classics (80s/90s)

### Tools
| Tool | Status |
|------|--------|
| Mortgage Calculator | ✅ Works |
| File Viewer | ✅ Works |
| Affiliate Cards | ✅ Works |

### LLM Providers
| Provider | Model | Use |
|----------|-------|-----|
| **Gemini** (default) | gemini-1.5-flash | Video search, recommendations |
| Groq | llama-3.1-8b | Fast fallback |

---

## 📹 CONTENT SOURCING (For Evaluation)

> **Pending API access** — sources to scrape/curate when ready

### Sports Legends on Craft
| Person | Topic | Search Terms | Priority |
|--------|-------|--------------|----------|
| **Kobe Bryant** | Basketball, Mamba Mentality | "kobe bryant basketball analysis", "kobe detail espn", "kobe mamba mentality interview" | 🔥 High |
| **Ted Williams** | Hitting, Science of Batting | "ted williams hitting", "ted williams science of hitting interview" | 🔥 High |
| **Tony Gwynn** | Hitting, Approach | "tony gwynn hitting", "tony gwynn interview approach" | 🔥 High |
| **Bill Belichick** | Football, Coaching | "belichick breakdown", "belichick press conference", "a football life belichick" | 🔥 High |
| **Vince Lombardi** | Leadership, Football | "vince lombardi speech", "lombardi documentary", "lombardi leadership" | 🔥 High |

### Library of Congress / Archival
| Source | Content Type | Notes |
|--------|--------------|-------|
| **Library of Congress YouTube** | @LibraryOfCongress | Historical footage, oral histories, Americana |
| **National Archives** | @USNationalArchives | Government films, WWII, space program |
| **British Pathé** | @britishpathe | Newsreels, 20th century history |
| **AP Archive** | @aparchive | News footage, sports history |

### Channels to Scrape (When Ready)
```bash
# Sports Legends / Craft
yt-dlp --flat-playlist -i --print "%(id)s|%(title)s" "https://www.youtube.com/results?search_query=kobe+bryant+detail+espn" | head -20
yt-dlp --flat-playlist -i --print "%(id)s|%(title)s" "https://www.youtube.com/results?search_query=ted+williams+hitting+interview" | head -20
yt-dlp --flat-playlist -i --print "%(id)s|%(title)s" "https://www.youtube.com/results?search_query=tony+gwynn+interview+hitting" | head -20
yt-dlp --flat-playlist -i --print "%(id)s|%(title)s" "https://www.youtube.com/results?search_query=belichick+breakdown+football" | head -20
yt-dlp --flat-playlist -i --print "%(id)s|%(title)s" "https://www.youtube.com/results?search_query=vince+lombardi+speech+documentary" | head -20

# Library of Congress
yt-dlp --flat-playlist -i --print "%(id)s|%(title)s" "https://www.youtube.com/@LibraryOfCongress/videos" | head -50

# National Archives  
yt-dlp --flat-playlist -i --print "%(id)s|%(title)s" "https://www.youtube.com/@USNationalArchives/videos" | head -50

# Classic Sports
yt-dlp --flat-playlist -i --print "%(id)s|%(title)s" "https://www.youtube.com/@NFLThrowback/videos" | head -50
yt-dlp --flat-playlist -i --print "%(id)s|%(title)s" "https://www.youtube.com/@NBAHistory/videos" | head -50
```

### More Candid Interview Sources
| Person/Topic | Search | Notes |
|--------------|--------|-------|
| **Muhammad Ali** | "ali interview", "ali parkinson cavett" | Classic interviews |
| **Larry Bird/Magic** | "bird magic documentary", "bird magic interview" | Rivalry content |
| **Bo Jackson** | "bo jackson 30 for 30", "bo knows" | ESPN Films |
| **Jim Brown** | "jim brown interview", "jim brown documentary" | Social/sports |
| **Deion Sanders** | "deion sanders interview", "prime time" | Two-sport legend |

### 30 for 30 / Documentary Series
```
ESPN 30 for 30 (search individual titles)
NFL Films (A Football Life series)
NBA TV (Open Court, Hardwood Classics)
```

---

## 🎵 UK MUSIC SCENE / ART SOURCING (Wired!)

> **Bristol sound, trip-hop, street art, 90s club culture**

### Massive Attack / Bristol Scene ✅ ADDED
| Video ID | Title | Notes |
|----------|-------|-------|
| `pt2e4wv7Q_o` | Unfinished: The Making of Massive Attack | Deep doc |
| `c-kxNI4Jk5s` | Massive Attack Documentary x4 Complete | Full compilation |
| `8MY71_JMDfo` | Sounds Of The West - Bristol (Wild Bunch/Tricky/Portishead) | Essential |
| `0UcXH6HRwXI` | Massive Attack Interview - Making Of Heligoland | 2010 studio |
| `Bq8EQlikuFE` | Massive Attack 1994 Interview | Archive footage |
| `rPF0bMWC4WY` | Massive Attack - Mezzanine Interview 1998 | Classic era |

### Björk ✅ ADDED
| Video ID | Title | Notes |
|----------|-------|-------|
| `IqqBcLcdhJM` | Ultimate Björk - Part 1 (Iceland, Sugarcubes) | Career doc |
| `1aBFm0q8Q3w` | Björk Shows Her Home and City | Personal tour |
| `Dk8mkrpwlcQ` | Bjork 1993 Interview At Home In Iceland | MTV classic |
| `4OFBJcU3Kr8` | Björk on Conan O'Brien | Solo career start |

### Banksy / Street Art ✅ ADDED
| Video ID | Title | Notes |
|----------|-------|-------|
| `E8HiOiXQUmg` | Banksy: Most Famous Anonymous Street Artist | Full doc |
| `IqVXThss1z4` | Exit Through the Gift Shop | THE Banksy film |
| `xrC9IhPUkJg` | Banksy Does New York (2014) | NYC residency |
| `tbGs1xqsAyo` | Extremely Rare Banksy Interview from 2003 | Rare! |

### Ministry of Sound / 90s Club Culture ✅ ADDED
| Video ID | Title | Notes |
|----------|-------|-------|
| `C5CrWTcCSFw` | Ministry of Sound Documentary Channel 4 (2001) | Essential |
| `tyntZ2CXs5c` | Clubbing at 1990s Ministry of Sound | Archive footage |
| `eP0Jj89nK-Q` | Better Days: The Story of UK Rave | Amazon doc |
| `wmz9eeKxot4` | 1990s News Report on Ministry of Sound | Period piece |
| `Ao5fuBflTzE` | Tony Humphries - MoS Sessions Vol 1 (1993) | Classic mix |

### Still To Source
| Artist/Topic | Search Terms | Priority |
|--------------|--------------|----------|
| **Tricky** | "tricky interview documentary", "tricky unkle" | 🔜 |
| **Portishead** | "portishead documentary", "beth gibbons interview" | 🔜 |
| **UNKLE** | "unkle james lavelle documentary", "unkle psyence fiction" | 🔜 |
| **Goldie** | "goldie documentary", "metalheadz" | 🔜 |
| **Aphex Twin** | "aphex twin interview", "richard d james" | 🔜 |
| **Chemical Brothers** | "chemical brothers documentary", "dig your own hole" | 🔜 |
| **Fatboy Slim** | "fatboy slim documentary", "norman cook" | 🔜 |

---

## 📼 VHS ERA / 90s TV SOURCING (For Evaluation)

> **Nostalgia gold mine** — VHS tracking errors welcome

### Classic Boxing (Oldest Forward)
| Era | Fighters | Search Terms | Notes |
|-----|----------|--------------|-------|
| **1950s** | Joe Louis, Rocky Marciano | "joe louis marciano 1951", "rocky marciano documentary" | Black & white era |
| **1960s-70s** | Ali, Frazier, Foreman | "ali frazier thrilla manila", "rumble in the jungle full fight", "champions forever" | Golden heavyweight era |
| **1980s Four Kings** | Hagler, Hearns, Leonard, Duran | "hagler hearns round 1", "leonard duran no mas", "fabulous four boxing" | Best middleweight era |
| **Late 80s-90s** | Tyson, Holyfield, Lewis | "tyson holmes 1988", "tyson spinks full fight", "tyson knockouts 80s" | Peak Tyson |

### VH1 Behind The Music
| Artist | Video ID | Notes |
|--------|----------|-------|
| Pantera | `roWGRqmNrOQ` | Remastered HD |
| Poison | `JCZjYbkEnRs` | 2001 episode |
| Aerosmith | `g7uF170I-xw` | 2002 documentary |
| Motörhead | `12sZRIwJDvE` | Full episode |
| Genesis | `cbAa566qE_4` | Full episode |
| Guns N Roses | `WJSQ3StGzJs` | 2004 documentary |
| Styx | `eEG5s_rwbv4` | Full episode |
| Mary J Blige | `fwLxek6saFw` | Full episode |
| Lynyrd Skynyrd | `yJu6vJl43ds` | Full episode |
| Journey | `KWLqLcpmOXc` | 2001 documentary HD |
| Def Leppard | `DjcYOmn4lsc` | Full episode |
| Heart | `v8ATnZFOVGg` | Full episode |
| Foreigner | `o7KhBULPLUk` | 2002 complete |
| Go-Go's | `f1Y3h-qx1bU` | Rise of all-girl rock band |
| Vanilla Ice | `UPhr6_daQrE` | 1999 good quality |
| Ozzy | `7KnXgwhLJN0` | Full episode |
| Bad Company | `SXUdOEtCbik` | Documentary |
| Creed | `_9zwiYvDtmY` | The Beginning |

### 90s Daytime Talk Shows
| Show | Search Terms | Status |
|------|--------------|--------|
| **Jerry Springer** | "jerry springer full episode classic" | 📋 Evaluate - lots available |
| **Ricki Lake** | "ricki lake show full episode" | 📋 Evaluate - decent selection |
| **Maury** | "maury povich full episode classic" | 📋 Evaluate - paternity classics |
| **Jenny Jones** | "jenny jones show full episode" | 🔜 Search |
| **Sally Jessy Raphael** | "sally jessy raphael full episode" | 🔜 Search |
| **Geraldo** | "geraldo rivera talk show" | 🔜 Search |
| **Phil Donahue** | "donahue show full episode" | 🔜 Search |
| **Montel Williams** | "montel williams full episode" | 🔜 Search |

### VHS Nostalgia / Commercials
| Content Type | Video IDs | Notes |
|--------------|-----------|-------|
| 90s Commercials Vol 556 | `ZlaZUTxRojc` | Part 1 of 2 |
| 80s & 90s Commercials V3 | `WVNqmxS6IiE` | One hour |
| 80s & 90s Commercials V1 | `PTOa3IdMXM0` | One hour |
| 90 Min Pure Nostalgia | `pQ2jtyVKWhc` | Retro TV Vol 500 |
| Most Nostalgic 90s Tape | `gWy2SuD8BYo` | From old VHS |
| Late 90s Compilation #50 | `OatUMBSDQyo` | 75 minutes |
| 80s Nostalgia Overload | `EehfM9ggooY` | V555 |
| 2 Hours 80s/90s | `KdrRQyKJodo` | SFA Vol 03 |
| Most Nostalgic 80s | `Lyu__qMBlzQ` | Vol 509 |
| 2+ Hours 80s/90s | `jJikWv3HrLE` | SFA Vol 02 |
| High Quality 90s Ads | `ePl-UGLuOH0` | V537 |
| Retro Halloween VHS | `VS2wP3OrzuI` | 80s 90s Holiday |

### yt-dlp Commands (When Ready)
```bash
# Classic Boxing
yt-dlp --flat-playlist -i --print "%(id)s|%(title)s" "https://www.youtube.com/results?search_query=ali+frazier+foreman+full+fight" | head -20
yt-dlp --flat-playlist -i --print "%(id)s|%(title)s" "https://www.youtube.com/results?search_query=hagler+hearns+leonard+duran+full+fight" | head -20
yt-dlp --flat-playlist -i --print "%(id)s|%(title)s" "https://www.youtube.com/results?search_query=mike+tyson+knockout+full+fight+80s" | head -20

# VH1 Behind The Music
yt-dlp --flat-playlist -i --print "%(id)s|%(title)s" "https://www.youtube.com/results?search_query=vh1+behind+the+music+full+episode" | head -30

# 90s Talk Shows
yt-dlp --flat-playlist -i --print "%(id)s|%(title)s" "https://www.youtube.com/results?search_query=jerry+springer+full+episode+classic" | head -20
yt-dlp --flat-playlist -i --print "%(id)s|%(title)s" "https://www.youtube.com/results?search_query=ricki+lake+full+episode" | head -20

# VHS Commercials
yt-dlp --flat-playlist -i --print "%(id)s|%(title)s" "https://www.youtube.com/results?search_query=80s+90s+commercials+nostalgia+vhs" | head -20
```

---

### Implementation Notes
- Each page can load a personality config from JSON
- Personality affects: tone, vocabulary, recommendations, affiliate focus
- Eventually: canned phrases, opening greetings, response styles per personality
- Cross-pollination: personalities can guest on each other's pages
- Layout: Always 4 cards (1 + 3 row), then LLM response below
- Each page can load a personality config from JSON
- Personality affects: tone, vocabulary, recommendations, affiliate focus
- Eventually: canned phrases, opening greetings, response styles per personality
- Cross-pollination: personalities can guest on each other's pages
- Layout: Always 4 cards (1 + 3 row), then LLM response below
