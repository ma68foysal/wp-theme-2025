/**
 * BANTU Plus SVOD Platform
 * Frontend JavaScript for video player, AJAX interactions, and dynamic features
 */

(function() {
	'use strict';

	console.log('[BANTU Plus] Initializing platform...');

	// ============================================
	// HLS Player Initialization
	// ============================================
	function initializeHLSPlayer(containerId, hlsUrl) {
		const container = document.getElementById(containerId);
		if (!container) {
			console.warn('[BANTU Plus] Player container not found:', containerId);
			return;
		}

		const video = container.querySelector('video') || document.createElement('video');
		video.id = 'bantu-player';
		video.controls = true;
		video.preload = 'metadata';

		if (!container.querySelector('video')) {
			container.appendChild(video);
		}

		// Check for HLS.js support
		if (typeof Hls === 'undefined') {
			console.warn('[BANTU Plus] HLS.js not loaded yet, retrying...');
			setTimeout(() => initializeHLSPlayer(containerId, hlsUrl), 500);
			return;
		}

		if (Hls.isSupported()) {
			const hls = new Hls({
				debug: false,
				enableWorker: true,
				lowLatencyMode: false,
			});

			hls.loadSource(hlsUrl);
			hls.attachMedia(video);

			hls.on(Hls.Events.MANIFEST_PARSED, function() {
				console.log('[BANTU Plus] HLS manifest parsed, playing...');
				video.play().catch(err => console.warn('[BANTU Plus] Auto-play failed:', err));
			});

			hls.on(Hls.Events.ERROR, function(event, data) {
				if (data.fatal) {
					console.error('[BANTU Plus] HLS Fatal Error:', data);
					hls.destroy();
				}
			});

			// Store reference for cleanup
			window.bantuHLS = hls;
		} else if (video.canPlayType('application/vnd.apple.mpegurl')) {
			// Fallback for native HLS support (Safari, iOS)
			video.src = hlsUrl;
			console.log('[BANTU Plus] Using native HLS support');
		}
	}

	// ============================================
	// Progress Tracking
	// ============================================
	function initializeProgressTracking(videoId) {
		const player = document.getElementById('bantu-player');
		if (!player) return;

		// Load saved progress
		const savedProgress = localStorage.getItem(`bantu_video_${videoId}_progress`);
		if (savedProgress) {
			player.currentTime = parseFloat(savedProgress);
		}

		// Save progress every 10 seconds
		let progressSaveTimer;
		player.addEventListener('timeupdate', function() {
			clearTimeout(progressSaveTimer);
			progressSaveTimer = setTimeout(() => {
				localStorage.setItem(`bantu_video_${videoId}_progress`, player.currentTime);
			}, 10000);
		});

		// Save on pause
		player.addEventListener('pause', function() {
			localStorage.setItem(`bantu_video_${videoId}_progress`, player.currentTime);
		});
	}

	// ============================================
	// Video Grid Interactions
	// ============================================
	function initializeVideoGrid() {
		const videoCards = document.querySelectorAll('.bantu-video-card');
		if (videoCards.length === 0) return;

		videoCards.forEach(card => {
			card.addEventListener('click', function(e) {
				e.preventDefault();
				const videoUrl = this.getAttribute('href') || this.getAttribute('data-video-url');
				if (videoUrl) {
					window.location.href = videoUrl;
				}
			});

			card.addEventListener('keydown', function(e) {
				if (e.key === 'Enter' || e.key === ' ') {
					e.preventDefault();
					this.click();
				}
			});
		});
	}

	// ============================================
	// AJAX Form Submission
	// ============================================
	function initializeFormHandlers() {
		const forms = document.querySelectorAll('.bantu-form');
		if (forms.length === 0) return;

		forms.forEach(form => {
			form.addEventListener('submit', function(e) {
				const submitBtn = this.querySelector('button[type="submit"]');
				if (submitBtn) {
					submitBtn.disabled = true;
					submitBtn.textContent = 'Processing...';
				}
			});
		});
	}

	// ============================================
	// Access Control - Paywall Check
	// ============================================
	function checkAccessControl() {
		const playerContainer = document.getElementById('bantu-player-container');
		if (!playerContainer) return;

		const isRestricted = playerContainer.getAttribute('data-restricted') === 'true';
		const userHasMembership = playerContainer.getAttribute('data-has-membership') === 'true';

		if (isRestricted && !userHasMembership) {
			// Show paywall
			const paywall = document.getElementById('bantu-paywall');
			if (paywall) {
				paywall.style.display = 'block';
				playerContainer.style.opacity = '0.3';
				playerContainer.style.pointerEvents = 'none';
				console.log('[BANTU Plus] Content restricted - paywall displayed');
			}
		}
	}

	// ============================================
	// Mobile Responsiveness
	// ============================================
	function initializeMobileMenu() {
		const menuToggle = document.querySelector('.bantu-menu-toggle');
		const mobileMenu = document.querySelector('.bantu-mobile-menu');

		if (menuToggle && mobileMenu) {
			menuToggle.addEventListener('click', function() {
				mobileMenu.classList.toggle('active');
				this.setAttribute('aria-expanded', 
					this.getAttribute('aria-expanded') === 'false' ? 'true' : 'false'
				);
			});

			// Close menu on link click
			mobileMenu.querySelectorAll('a').forEach(link => {
				link.addEventListener('click', () => {
					mobileMenu.classList.remove('active');
					menuToggle.setAttribute('aria-expanded', 'false');
				});
			});
		}
	}

	// ============================================
	// Video Search & Filter
	// ============================================
	function initializeSearch() {
		const searchInput = document.getElementById('bantu-search');
		if (!searchInput) return;

		let searchTimeout;
		searchInput.addEventListener('input', function(e) {
			clearTimeout(searchTimeout);
			const query = e.target.value.trim();

			searchTimeout = setTimeout(() => {
				if (query.length > 0) {
					performSearch(query);
				}
			}, 500);
		});
	}

	function performSearch(query) {
		// Send AJAX request to WordPress
		const data = new FormData();
		data.append('action', 'bantu_search_videos');
		data.append('query', query);
		data.append('nonce', bantuAjax.nonce);

		fetch(bantuAjax.ajaxUrl, {
			method: 'POST',
			body: data
		})
		.then(response => response.json())
		.then(result => {
			if (result.success) {
				updateSearchResults(result.data);
			}
		})
		.catch(error => console.error('[BANTU Plus] Search error:', error));
	}

	function updateSearchResults(results) {
		const grid = document.querySelector('.bantu-video-grid');
		if (!grid) return;

		grid.innerHTML = '';

		if (results.length === 0) {
			grid.innerHTML = '<p>No videos found.</p>';
			return;
		}

		results.forEach(video => {
			const card = document.createElement('div');
			card.className = 'bantu-video-card';
			card.innerHTML = `
				<img src="${video.thumbnail}" alt="${video.title}" class="bantu-video-card-image">
				<div class="bantu-video-card-overlay">
					<h3 class="bantu-video-card-title">${video.title}</h3>
					<p class="bantu-video-card-meta">${video.duration} min</p>
				</div>
			`;
			card.addEventListener('click', () => {
				window.location.href = video.url;
			});
			grid.appendChild(card);
		});
	}

	// ============================================
	// Horizontal Content Row Scrolling
	// ============================================
	function initializeContentRowScroll() {
		const contentRows = document.querySelectorAll('.bantu-content-row');
		if (contentRows.length === 0) return;

		contentRows.forEach(row => {
			// Add keyboard support for horizontal scrolling
			row.addEventListener('keydown', function(e) {
				if (e.key === 'ArrowLeft') {
					this.scrollBy({ left: -250, behavior: 'smooth' });
				} else if (e.key === 'ArrowRight') {
					this.scrollBy({ left: 250, behavior: 'smooth' });
				}
			});

			// Add scroll indicators
			const scrollIndicator = document.createElement('div');
			scrollIndicator.className = 'bantu-scroll-indicator';
			scrollIndicator.style.cssText = `
				position: absolute;
				top: 50%;
				right: 0;
				z-index: 10;
				background: rgba(229, 9, 20, 0.7);
				color: white;
				padding: 1rem;
				border-radius: 4px 0 0 4px;
				cursor: pointer;
				transition: background 0.3s ease;
			`;
			scrollIndicator.innerHTML = '→';
			scrollIndicator.addEventListener('click', function() {
				row.scrollBy({ left: 300, behavior: 'smooth' });
			});
			scrollIndicator.addEventListener('mouseover', function() {
				this.style.backgroundColor = 'rgba(229, 9, 20, 1)';
			});
			scrollIndicator.addEventListener('mouseout', function() {
				this.style.backgroundColor = 'rgba(229, 9, 20, 0.7)';
			});
		});
	}

	// ============================================
	// Initialize Everything on Document Ready
	// ============================================
	function initialize() {
		console.log('[BANTU Plus] Document ready, initializing components...');

		// Check if we're on a video player page
		if (document.getElementById('bantu-player-container')) {
			checkAccessControl();
		}

		// Initialize player if present
		const playerContainer = document.getElementById('bantu-player-container');
		if (playerContainer) {
			const hlsUrl = playerContainer.getAttribute('data-hls-url');
			const videoId = playerContainer.getAttribute('data-video-id');
			if (hlsUrl) {
				initializeHLSPlayer('bantu-player-container', hlsUrl);
				if (videoId) {
					initializeProgressTracking(videoId);
				}
			}
		}

		// Initialize other components
		initializeVideoGrid();
		initializeFormHandlers();
		initializeMobileMenu();
		initializeSearch();
		initializeContentRowScroll();

		console.log('[BANTU Plus] Initialization complete');
	}

	// Wait for DOM to be fully loaded
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', initialize);
	} else {
		initialize();
	}

	// Expose public API
	window.bantuPlus = {
		initializePlayer: initializeHLSPlayer,
		performSearch: performSearch,
	};

})();
