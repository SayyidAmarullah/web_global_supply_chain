# DATABASE DESIGN & DATA ARCHITECTURE
**Project:** GLOBAL SUPPLY CHAIN RISK INTELLIGENCE & TRADE DECISION SUPPORT PLATFORM

---

## 1. DATABASE OBJECTIVE & PRINCIPLES
The database is designed as an enterprise-grade, highly normalized relational structure. It acts as the single source of truth for both static master data (Countries, Ports) and hyper-dynamic transactional data (Shipment Telemetry, High-Frequency Commodity Prices).
* **Principles:** 
  * Strict Normalization (3NF) to eliminate data redundancy.
  * Modular Structure isolating temporal data (history/telemetry) from core entities to ensure fast query performance on active records.
  * Referential Data Integrity through enforced foreign keys and strict cascade rules.

---

## 2. CORE ENTITIES & CONCEPTUAL DESIGN

### User & Access Management
* **Users:** Stores authentication credentials, profile information, and account status.
* **Roles & Permissions:** Defines access levels (Administrator vs. standard User). A User has one Role; a Role has many Permissions (Many-to-Many via pivot).

### Geographical & Master Data
* **Countries:** The core geopolitical entity. Stores static data (ISO codes, region). It acts as the central hub connecting to all localized intelligence.
* **Ports:** Infrastructure entities belonging to a Country. Stores UN/LOCODE, capacity metrics, and geographic coordinates.

### Commodity Intelligence
* **Commodity Categories:** Classifications (e.g., Energy, Metals, Agriculture).
* **Commodities:** Specific assets (e.g., Crude Oil, Wheat). Linked to a Category.
* **Commodity Prices & History:** Temporal entities recording daily/hourly market valuations, supply/demand metrics, and market trends.

### Shipment Management
* **Shipments:** The central logistical transaction. Links to User, Origin (Country/Port), Destination (Country/Port), and Commodity.
* **Shipment Containers:** Represents physical TEUs assigned to a shipment.
* **Shipment Routes & Locations:** Geographical waypoints mapping the intended and actual path.
* **Shipment Timeline & Events:** An append-only ledger recording every status change (Created, Loaded, Transit, Arrived).
* **Shipment Redirections:** Records autonomous or manual changes in destination, storing the "reason" (linked to Risk/Weather alerts).

### Intelligence & Analysis
* **Weather Records & Forecasts:** Temporal data mapped to specific coordinates, Ports, or Countries.
* **Exchange Rates:** Forex pairs history.
* **Economic Indicators:** GDP, Inflation, and Output recorded periodically per Country.
* **News Articles & Sentiment:** Scraped data categorized by entity (Country/Commodity) with processed Sentiment Scores.
* **Risk Assessments:** Snapshots of computed Global Risk Scores for a specific entity at a specific time.
* **Trade Opportunities & Recommendations:** AI-generated simulations indicating profitability percentages for specific trade corridors.

### User Engagement
* **Watchlists (Favorites):** Many-to-Many relationships linking a User to specific Countries, Ports, Commodities, or Shipments for priority tracking.
* **Notifications & History:** Alert messages pushed to users based on triggered Watchlist thresholds.
* **Reports:** Metadata and file paths for generated PDF/Excel exports.
* **Audit & Activity Logs:** Immutable records of all system actions for security compliance.

---

## 3. ADVANCED DATA MODELS

### Shipment Data Model (The Supply Chain Flow)
A Shipment is the parent entity. It contains Many `Containers`. It is linked to One `Commodity`. It enforces dual geographical relationships: `Origin` (One Country, One Port) and `Destination` (One Country, One Port). 
During transit, the `Current Location` is updated in the `Shipment Tracking History`. If an AI triggers a change, a record is added to the `Redirect History`, and the active `Destination` is mutated. The `Timeline` records every state transition up to `Final Delivery`.

### Country Data Model (The Geopolitical Hub)
A `Country` acts as a central node. It has One-to-Many relationships with `Ports`, `Weather Records`, `Economic Indicators` (GDP/Inflation), and `News Articles`. The AI calculates and stores localized `Risk Scores` and `Opportunity Scores` linked directly to the Country ID.

### Commodity Data Model (The Market Hub)
A `Commodity` is linked to continuous `Historical Prices`. It maintains Many-to-Many relationships indicating `Top Export Countries` and `Top Import Countries`. It is the core determinant for `Shipment` value and acts as the trigger for `Trade Recommendations`.

### Risk & Trade Models
* **Risk Model:** Aggregates localized risk (Weather, Political, Currency, Commodity, Port) into a single `Overall Risk Score` entity, stamped with a timestamp and linked to a Country or Route.
* **Trade Model:** Simulates outcomes by linking a Buyer (Country), Supplier (Country), and Commodity. It records calculated Import Costs, Export Revenues, and stores the simulated `Trade Recommendation`.

### AI Decision Model
Stores the outputs of the AI engine. A `Recommendation History` record captures the context (e.g., "Storm in Port A"), the suggested entity (`Recommended Country`, `Recommended Port`), the predicted outcome, and the User's ultimate `Recommendation Result` (Accepted/Ignored).

---

## 4. DATA RELATIONSHIPS & INTEGRITY

* **One-to-One (1:1):** e.g., A `Shipment` to its `Current Location` tracker.
* **One-to-Many (1:N):** e.g., A `Country` to many `Ports`; A `User` to many `Shipments`.
* **Many-to-Many (N:M):** e.g., `Users` to `Favorite Countries` (requires a pivot table like `user_country_watchlists`).
* **Cascade Rules:** 
  * Strict `RESTRICT` on deletion of Master Data (e.g., cannot delete a Country if Ports or Shipments depend on it).
  * `CASCADE` delete on localized temporal data (e.g., deleting a Shipment cascades to delete its Tracking History).

---

## 5. INDEX STRATEGY & SCALABILITY

### Search & Optimization
* **B-Tree Indexes:** Applied to all Primary Keys and Foreign Keys to ensure rapid table joins.
* **Composite Indexes:** Created for frequent multi-column queries (e.g., filtering `Shipments` by `status` AND `destination_country_id`).
* **Time-Series Optimization:** For entities like `Commodity Prices` and `Tracking Records`, indexes heavily prioritize `timestamp` columns to accelerate historical charting and analytics.

### Scalability Architecture
The database is designed to handle thousands of shipments and millions of tracking/pricing rows.
* **Data Partitioning:** Temporal tables (Prices, Weather, Tracking) will be partitioned by Date (e.g., monthly partitions) to ensure the active dataset remains highly performant.
* **Data Retention & Archival:** 
  * Active data (current shipments, today's prices) is kept in hot storage.
  * Historical data older than 3 years (Completed Shipments, Old Reports, Historical News) is flagged for archival or moved to cold storage tables to prevent primary database bloat.
