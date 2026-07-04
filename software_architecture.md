# SOFTWARE ARCHITECTURE DOCUMENT (SAD)
**Project:** GLOBAL SUPPLY CHAIN RISK INTELLIGENCE & TRADE DECISION SUPPORT PLATFORM

---

## 1. ARCHITECTURE OBJECTIVE
The architecture of this platform is designed to be highly modular, scalable, and resilient. It supports real-time global intelligence aggregation, autonomous AI decision support, and active shipment management. Every module functions independently while integrating seamlessly to produce synthesized actionable insights.

---

## 2. OVERALL ARCHITECTURE LAYERS
The system employs an N-Tier, domain-driven architecture to ensure maximum separation of concerns:

1. **Presentation Layer:** The user-facing "Mission Control" interface. Handles UI/UX, responsive layouts, interactive GIS mapping, and dynamic data visualization.
2. **Application Layer:** Manages routing, user sessions, middleware (authentication/authorization), and intercepts requests from the Presentation Layer to delegate to business logic.
3. **Business Logic Layer:** The core brain of the platform. Contains the Trade Engine, Risk Engine, and AI Decision Support Engine.
4. **Service Layer:** Connects the Business Logic to data providers. Manages independent services (e.g., ShipmentService, IntelligenceService) ensuring domain isolation.
5. **Repository Layer:** Acts as an abstraction layer between the Services and the Data. Ensures the business logic is agnostic to the underlying database technology.
6. **Data Layer:** The persistence layer. Stores shipments, historical intel, logs, user profiles, and application state.
7. **External API Layer:** Manages outbound communication with third-party providers (Weather, Forex, Marine Tracking) with built-in retry mechanisms and rate-limiting.

---

## 3. SYSTEM MODULES ARCHITECTURE

* **Dashboard:** The central nervous system aggregator. Subscribes to updates from all other modules to present real-time KPIs.
* **Shipment:** Manages the CRUD and lifecycle of cargo. Operates state machines for shipment status.
* **Country & Port:** Static master data enriched by dynamic intelligence (GDP, capacity).
* **Commodity & Currency:** High-frequency temporal modules tracking price volatility and exchange rates.
* **Weather & News:** Unstructured and semi-structured data processors that feed the Risk Engine.
* **Risk Engine:** A background computational module analyzing inputs to output normalized risk vectors.
* **Trade Engine:** A computational module simulating financial outcomes for imports and exports.
* **Reports:** A document generation service detached from core processing to avoid blocking main threads.
* **Administration:** RBAC-secured backend module for system configuration.
* **Notifications & Watchlist:** Asynchronous pub/sub modules that dispatch alerts based on user-defined thresholds.

---

## 4. MODULE COMMUNICATION
Communication is unidirectional and dependency-injected to prevent tight coupling.

**Example Flow (Smart Redirection):**
External API Layer (Weather Data) $\rightarrow$ Weather Module $\rightarrow$ Risk Engine (Calculates new risk) $\rightarrow$ Shipment Module (Detects threshold breach) $\rightarrow$ Recommendation Engine (Calculates alternatives) $\rightarrow$ Notification Module $\rightarrow$ Dashboard.

**Dependencies:**
- The *Risk Engine* depends on Weather, News, Port, and Currency Modules.
- The *Trade Engine* depends on Commodity, Currency, Country, and Port Modules.
- The *AI Decision Engine* depends on both Risk and Trade Engines.

---

## 5. AI DECISION ENGINE ARCHITECTURE
The AI Engine functions as a heuristic evaluation system.
* **Mechanism:** It continuously polls the Trade Opportunity Score and Global Risk Score.
* **Destination/Shipment Recommendation:** When evaluating a route, the Engine simulates 100+ alternative routes using current global parameters. It filters out routes exceeding maximum Risk Scores and sorts the remainder by expected Profit.
* **Alternative Country/Port:** Compares historical throughput, current congestion, and geopolitical sentiment to recommend logical alternatives accompanied by data-driven justifications.

---

## 6. GLOBAL RISK SCORE & TRADE OPPORTUNITY SCORE

### Global Risk Score
* **Inputs:** Meteorological severity (Rain/Storm/Wind), Economic volatility (Inflation/Exchange Rate), Infrastructure constraints (Port Congestion, Shipping Delay), and Sentiment (Political News).
* **Processing:** Each input is normalized to a 0-100 scale, weighted by severity, and aggregated.
* **Categories:** Low, Medium, High, Critical.
* **Business Rule:** If Risk hits "Critical", the system automatically triggers a Redirection calculation.

### Trade Opportunity Score
* **Inputs:** Commodity Price disparity, Target Market Demand, Exchange Rate leverage, Import Tax penalties, Shipping Costs, and Country GDP.
* **Processing:** Computes a net margin percentage probability.
* **Output:** Opportunity Score (High/Medium/Low).
* **Business Rule:** Highly volatile countries receive an opportunity penalty.

---

## 7. LIVE SHIPMENT ARCHITECTURE
* **Creation:** Generates a unique tracking hash and initializes an empty Timeline.
* **Tracking & Monitoring:** A scheduled background job queries External Marine APIs to update current coordinates.
* **Timeline (Event Sourcing):** Every status change is an append-only event in the History module to guarantee auditability.
* **Redirection:** Does not mutate the original destination record; instead, appends a "Redirected" event and points to a new active route state.

---

## 8. WORLD MAP ARCHITECTURE (GIS)
* **Interactive Map:** The base canvas utilizing deep-space vector tiles.
* **Data Layers:** 
  - *Country Layer:* Polygons colored by Opportunity/Risk heatmaps.
  - *Port/Commodity Layers:* Clickable nodes distributing localized intel.
  - *Shipment Layer:* Live markers interpolating between origin and destination.
  - *Weather/Risk Layers:* Overlays showing storm systems or conflict zones.
* **Interaction Flow:** Presentation Layer catches click events $\rightarrow$ Queries Application Layer $\rightarrow$ Service Layer fetches specific entity intel $\rightarrow$ Renders dynamic glass-panel overlay.

---

## 9. EXTERNAL SERVICES INTEGRATION
The system conceptually integrates via REST/GraphQL with:
- **Marine Tracking Service:** For live vessel coordinates (AIS data).
- **Weather Service:** For global meteorological grids.
- **Exchange Rate / Commodity Market Services:** For high-frequency financial ticker data.
- **Economic / News Services:** For macroeconomic indicators and RSS/Sentiment feeds.
*(Note: Integration uses an Adapter pattern, ensuring the core system is agnostic to the specific external provider).*

---

## 10. SECURITY ARCHITECTURE
* **Authentication:** Secure credential verification with robust session management.
* **Authorization:** Strict RBAC (Role-Based Access Control) isolating User data from Administrator endpoints.
* **API Security:** Rate limiting, payload sanitization, and parameterized querying to prevent injection.
* **Audit Log:** Immutable logging of all sensitive actions (e.g., Shipment Redirection).
* **Error Handling:** Graceful degradation. If the Weather Service fails, the system warns the user rather than crashing the Risk Engine.

---

## 11. SCALABILITY
* **More Data (Countries/Commodities/Shipments):** The Repository layer allows vertical scaling of the database and implementation of caching (e.g., Redis) for high-read master data.
* **More APIs:** The External API layer uses job queues (asynchronous processing) to handle incoming data streams without blocking user interaction.
* **Future AI Features:** The Business Logic Layer's modularity ensures new AI models can be plugged in as standalone services without rewriting the core Shipment engine.

---

## 12. SYSTEM WORKFLOW
1. **User** logs into the platform.
2. Enters the **Dashboard** (Command Center).
3. Conducts **Country & Commodity Analysis** to identify opportunities.
4. Executes a **Trade Analysis** simulation.
5. Initiates a **Shipment**.
6. The system begins **Tracking**.
7. In transit, the system pushes an **AI Recommendation** due to an emerging storm.
8. The User makes a **Decision** to redirect.
9. Post-delivery, the User exports **Reports** for executive review.

---

## 13. ROLE ARCHITECTURE
* **Administrator:** 
  - *Responsibilities:* System health, global parameters, user oversight.
  - *Permissions:* Full CRUD on master data (Countries, Ports, Commodities).
  - *Accessible Modules:* All modules + Administration.
* **User:** 
  - *Responsibilities:* Executing and monitoring import/export operations.
  - *Permissions:* CRUD on their own Shipments, Read-Only on Global Intelligence.
  - *Accessible Modules:* Dashboard, Shipments, Intelligence Modules, Reports.
