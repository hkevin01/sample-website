// A. ACCESSIBILITY TEXT RESIZER PIPELINE
let currentScale = 100;
document.getElementById( 'font-inc' ).addEventListener( 'click', () => {
	currentScale += 10;
	document.documentElement.style.setProperty( '--font-scale', currentScale + '%' );
} );
document.getElementById( 'font-dec' ).addEventListener( 'click', () => {
	if ( currentScale > 80 ) {
		currentScale -= 10;
		document.documentElement.style.setProperty( '--font-scale', currentScale + '%' );
	}
} );

// B. THEME AND HIGH CONTRAST FLIP MECHANIC
document.getElementById( 'contrast-toggle' ).addEventListener( 'click', () => {
	const currentTheme = document.documentElement.getAttribute( 'data-theme' );
	if ( currentTheme === 'high-contrast' ) {
		document.documentElement.removeAttribute( 'data-theme' );
	} else {
		document.documentElement.setAttribute( 'data-theme', 'high-contrast' );
	}
} );

// C. NATIVE FRAMEWORK-FREE SLIDER CORE
const track = document.getElementById( 'slider-track' );
const slides = document.querySelectorAll( '.slide' );
let currentSlideIdx = 0;
function updateSliderPosition() {
	track.style.transform = `translateX(-${ currentSlideIdx * 100 }%)`;
}
const nextBtn = document.getElementById( 'slide-next' );
const prevBtn = document.getElementById( 'slide-prev' );
if ( nextBtn && prevBtn && track && slides.length ) {
	nextBtn.addEventListener( 'click', () => {
		currentSlideIdx = ( currentSlideIdx + 1 ) % slides.length;
		updateSliderPosition();
	} );
	prevBtn.addEventListener( 'click', () => {
		currentSlideIdx = ( currentSlideIdx - 1 + slides.length ) % slides.length;
		updateSliderPosition();
	} );
}

// D. LIVE OPERATIONAL STATUS POLLING (backed by the rangefinder/v1/status REST route)
function refreshLiveStatus() {
	const statusBadge = document.getElementById( 'live-status' );
	if ( ! statusBadge || typeof rangefinderData === 'undefined' ) {
		return;
	}
	fetch( rangefinderData.statusEndpoint )
		.then( ( response ) => response.json() )
		.then( ( data ) => {
			statusBadge.textContent = data.label;
			statusBadge.className = 'status-badge ' + data.class;
		} )
		.catch( () => {
			// Server-rendered status remains as a graceful fallback on network failure.
		} );
}
setInterval( refreshLiveStatus, 60000 );

// E. STRIPE CHECKOUT FOR MERCHANDISE
document.querySelectorAll( '.buy-now-btn' ).forEach( ( button ) => {
	button.addEventListener( 'click', () => {
		const errorEl = document.getElementById( 'checkout-error' );
		if ( errorEl ) {
			errorEl.style.display = 'none';
			errorEl.textContent = '';
		}
		button.disabled = true;
		button.textContent = 'Redirecting…';

		fetch( rangefinderData.checkoutEndpoint, {
			method: 'POST',
			headers: { 'Content-Type': 'application/json' },
			body: JSON.stringify( { item_id: button.dataset.itemId } ),
		} )
			.then( ( response ) => response.json().then( ( data ) => ( { ok: response.ok, data } ) ) )
			.then( ( { ok, data } ) => {
				if ( ok && data.checkout_url ) {
					window.location.href = data.checkout_url;
					return;
				}
				throw new Error( data.message || 'Unable to start checkout.' );
			} )
			.catch( ( err ) => {
				if ( errorEl ) {
					errorEl.textContent = err.message;
					errorEl.style.display = 'block';
				}
				button.disabled = false;
				button.textContent = 'Buy Now';
			} );
	} );
} );
