# 🌐 GlobalChain - Enterprise Logistics Intelligence

![GlobalChain Banner](https://via.placeholder.com/1200x400.png?text=GlobalChain+-+Logistics+Intelligence)

**GlobalChain** is a comprehensive Enterprise Logistics Intelligence platform built with Laravel. It is designed to provide real-time tracking, macro-economic analytics, risk assessment, and AI-driven decision support for global supply chain and maritime trade operations.

## Key Features

### Core Modules & Dashboards
*   **Global Mission Control:** A centralized command center displaying real-time KPIs, active transit fleets, global risk index, and trade opportunities.
*   **Dynamic Glassmorphism UI:** Built with enterprise-grade UI/UX standards, featuring a sleek dark-aurora theme and responsive glassmorphism effects.
*   **Multi-Language & Auto-Translate:** Integrated multi-language support (Indonesian, English, Spanish, etc.) utilizing a specialized `MutationObserver` to ensure Google Material Symbols remain intact during translation.
*   **Global Search Engine:** Instantly search across shipments, ports, commodities, and countries from anywhere in the platform.

### Supply Chain Management
*   **Shipment Intel:** End-to-end CRUD management for tracking global cargo routes, status, and operational metrics.
*   **Interactive Global Trade Map:** Real-time spatial visualization using **Leaflet.js**, mapping vessel coordinates, major ports, weather alerts, and country risk levels.
*   **Financial Risk & Revenue Simulation:** Automated calculations for estimated cargo revenue, operational costs, and profit margins.

### Intelligence & AI Analytics
*   **AI Decision Support Engine:** Autonomous algorithmic engine recommending optimal shipping routes based on geopolitical tensions, distance, and weather anomalies.
*   **Risk Scoring Engine:** Real-time risk synthesis aggregating Weather Anomalies (30%), Inflation Rates (20%), FX Volatility (10%), and Global News Sentiment (40%).
*   **Live Weather Integration:** Real-time meteorological data at ports and sea routes powered by the **Open-Meteo API**.
*   **Global Port Intelligence:** Monitor global port congestion, average wait times, and operational statuses via an interactive congestion heatmap.

### Macroeconomic & Market Monitoring
*   **Global Country Dashboard:** In-depth economic profiles utilizing **REST Countries** and **World Bank APIs** (GDP, Inflation, Demographics).
*   **Country & Commodity Comparison Engine:** Head-to-head visual analytics tools to compare macroeconomics or asset price volatility (e.g., Crude Oil WTI vs. Brent) for strategic arbitrage.
*   **Advanced FX Analytics:** Real-time currency exchange monitoring with 30-day volatility trends and relative power radar charts.
*   **Global News RSS:** Automated supply chain news aggregator equipped with Lexicon-based **AI Sentiment Analysis** (Positive, Negative, Neutral).

### Security & Administration
*   **Role-Based Access Control (RBAC):** Strict authorization layers differentiating **Administrators**, **Importers**, **Exporters**, and standard **Users**.
*   **Admin Control Center:** Exclusive panel for system configuration, user management, and API health monitoring.
*   **Immutable System Audit Logs:** Continuous tracking of critical actions (login attempts, data exports, config changes) to ensure operational accountability.

---

## Tech Stack

*   **Framework:** [Laravel 11](https://laravel.com/) (PHP)
*   **Frontend:** Blade Templates, [Tailwind CSS](https://tailwindcss.com/), Bootstrap 5 (Grid/Utilities)
*   **Database:** MySQL
*   **Maps & Geospatial:** [Leaflet.js](https://leafletjs.com/)
*   **Charts & Visualization:** Chart.js, ApexCharts (Optional)
*   **Third-Party APIs:** Open-Meteo API, World Bank API, REST Countries, ExchangeRate API, Google News RSS.

---

## Installation & Setup

Follow these steps to run the project on your local environment (e.g., using Laragon, XAMPP, or Laravel Herd).

1.  **Clone the repository:**
    ```bash
    git clone https://github.com/yourusername/globalchain.git
    cd globalchain
    ```

2.  **Install PHP dependencies:**
    ```bash
    composer install
    ```

3.  **Install Node.js dependencies:**
    ```bash
    npm install
    npm run build
    ```

4.  **Environment Setup:**
    Duplicate the `.env.example` file and rename it to `.env`.
    ```bash
    cp .env.example .env
    ```
    Update your `.env` file with your database credentials:
    ```env
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=webglobalchain
    DB_USERNAME=root
    DB_PASSWORD=
    ```

5.  **Generate Application Key:**
    ```bash
    php artisan key:generate
    ```

6.  **Run Migrations & Seeders (Important for Roles & Mock Data):**
    ```bash
    php artisan migrate --seed
    ```

7.  **Start the Local Development Server:**
    ```bash
    php artisan serve
    ```
    *The application will be accessible at `http://localhost:8000` or your configured local domain (e.g., `http://webglobalchain.test`).*

---

## Screenshots
*(You can replace these placeholder links with actual image paths once uploaded to your repository)*

*   **Global Mission Control**
    ![Mission Control](docs/mission_control.png)
*   **Global Trade Map**
    ![Trade Map](docs/trade_map.png)
*   **AI Decision Engine**
    ![AI Decision Engine](docs/ai_decision.png)
*   **Country Comparison Engine**
    ![Country Comparison](docs/country_comparison.png)

---

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Contributing

Contributions, issues, and feature requests are welcome! Feel free to check the [issues page](https://github.com/yourusername/globalchain/issues).
