import './stimulus_bootstrap.js';
import './styles/app.css';
import { Turbo } from '@hotwired/turbo';

// Disable Turbo Drive globally — it breaks Bootstrap dropdowns,
// inline scripts with HTML entities, and Symfony form_login redirects.
// Turbo Streams (real-time updates via Mercure) are unaffected.
Turbo.session.drive = false;

// Belt-and-suspenders: cancel any visit that slips through (e.g. cached snapshots).
document.addEventListener('turbo:before-visit', (e) => e.preventDefault());
