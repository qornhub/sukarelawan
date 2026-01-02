import './bootstrap';
import './echo';
import { initAttendanceRealtime } from './echo-listener';

// initialize DOM-ready: the blade will set window.__EVENT_ID
document.addEventListener('DOMContentLoaded', () => {
  if (window.__EVENT_ID) {
    initAttendanceRealtime(window.__EVENT_ID);
  }
});