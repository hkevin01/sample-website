import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap-icons/font/bootstrap-icons.css';
import 'bootstrap';
import './styles.css';

const currentYear = document.querySelector('#current-year');
if (currentYear) {
  currentYear.textContent = new Date().getFullYear();
}
