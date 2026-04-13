/**
 * BANTU Plus - Anti-Piracy Protection
 * Prevents video downloads, screen recording, and unauthorized sharing
 */

(function() {
	'use strict';

	console.log('[BANTU Plus] Anti-piracy protection loaded');

	// ============================================
	// Disable Right-Click on Video Player
	// ============================================
	function disableRightClick() {
		if ( ! bantuAntiPiracy.disableRightclick ) return;

		const player = document.getElementById( 'bantu-player' );
		if ( ! player ) return;

		player.addEventListener( 'contextmenu', function(e) {
			e.preventDefault();
			console.log( '[BANTU Plus] Right-click attempt blocked' );
			logAccess( 'right_click_blocked' );
			return false;
		} );

		console.log( '[BANTU Plus] Right-click protection enabled' );
	}

	// ============================================
	// Disable Download Controls
	// ============================================
	function disableDownloads() {
		if ( ! bantuAntiPiracy.disableDownload ) return;

		const video = document.getElementById( 'bantu-player' );
		if ( ! video ) return;

		// Add controlsList attribute
		video.setAttribute( 'controlsList', 'nodownload' );

		// Disable keyboard shortcuts
		document.addEventListener( 'keydown', function(e) {
			// Ctrl+S, Cmd+S
			if ( ( e.ctrlKey || e.metaKey ) && e.key === 's' ) {
				e.preventDefault();
				logAccess( 'download_attempt' );
				return false;
			}
		} );

		console.log( '[BANTU Plus] Download protection enabled' );
	}

	// ============================================
	// Disable Screen Recording & Casting
	// ============================================
	function disableScreenRecording() {
		if ( ! bantuAntiPiracy.disableScreenRecord ) return;

		const video = document.getElementById( 'bantu-player' );
		if ( ! video ) return;

		// Disable Picture-in-Picture
		video.disablePictureInPicture = true;

		// Hide PiP button
		video.addEventListener( 'play', function() {
			const pipButton = document.querySelector( 'button[aria-label*="Picture"]' );
			if ( pipButton ) {
				pipButton.style.display = 'none';
			}
		} );

		// Prevent AirPlay/Chromecast
		video.removeAttribute( 'webkit-playsinline' );
		video.setAttribute( 'playsinline', 'true' );

		// Monitor for screen capture attempts
		if ( navigator.mediaDevices && navigator.mediaDevices.getDisplayMedia ) {
			document.addEventListener( 'keydown', function(e) {
				// Prevent common screen recording shortcuts
				if ( ( e.ctrlKey || e.metaKey ) && e.shiftKey && e.key === 'S' ) {
					e.preventDefault();
					logAccess( 'screen_record_blocked' );
				}
			} );
		}

		console.log( '[BANTU Plus] Screen recording protection enabled' );
	}

	// ============================================
	// Prevent Copying & Dragging
	// ============================================
	function preventContentCopy() {
		const player = document.getElementById( 'bantu-player-container' );
		if ( ! player ) return;

		// Disable text selection
		player.style.userSelect = 'none';
		player.style.webkitUserSelect = 'none';
		player.style.mozUserSelect = 'none';

		// Disable drag and drop
		player.addEventListener( 'dragstart', function(e) {
			e.preventDefault();
			logAccess( 'drag_attempt' );
		} );

		// Disable copy/paste
		player.addEventListener( 'copy', function(e) {
			e.preventDefault();
			logAccess( 'copy_attempt' );
		} );

		console.log( '[BANTU Plus] Content copy protection enabled' );
	}

	// ============================================
	// Monitor Video Playback
	// ============================================
	function monitorPlayback() {
		const video = document.getElementById( 'bantu-player' );
		if ( ! video ) return;

		video.addEventListener( 'play', function() {
			const videoId = document.getElementById( 'bantu-player-container' ).dataset.videoId;
			logAccess( 'play', videoId );
		} );

		video.addEventListener( 'pause', function() {
			const videoId = document.getElementById( 'bantu-player-container' ).dataset.videoId;
			logAccess( 'pause', videoId );
		} );

		video.addEventListener( 'seeking', function() {
			const videoId = document.getElementById( 'bantu-player-container' ).dataset.videoId;
			logAccess( 'seek', videoId );
		} );

		// Monitor for speed changes
		video.addEventListener( 'ratechange', function() {
			if ( this.playbackRate > 1.5 ) {
				console.log( '[BANTU Plus] Suspicious playback speed detected' );
				logAccess( 'speed_abuse' );
			}
		} );

		console.log( '[BANTU Plus] Playback monitoring enabled' );
	}

	// ============================================
	// Access Logging
	// ============================================
	function logAccess( action, videoId = null ) {
		if ( ! bantuAntiPiracy.enableLogging ) return;

		if ( ! videoId ) {
			const container = document.getElementById( 'bantu-player-container' );
			videoId = container ? container.dataset.videoId : null;
		}

		const data = new FormData();
		data.append( 'action', 'bantu_log_access' );
		data.append( 'action_type', action );
		data.append( 'video_id', videoId );
		data.append( 'nonce', bantuAntiPiracy.nonce );

		fetch( bantuAntiPiracy.ajaxUrl, {
			method: 'POST',
			body: data,
		} )
		.catch( err => console.log( '[BANTU Plus] Logging error:', err ) );
	}

	// ============================================
	// Prevent Common Piracy Tools
	// ============================================
	function detectPiracyTools() {
		// Check for common screen recording tools in user agent
		const ua = navigator.userAgent.toLowerCase();
		const piracyIndicators = [
			'screen-capture',
			'recording',
			'recordfromscreen',
			'netscape',
		];

		piracyIndicators.forEach( indicator => {
			if ( ua.includes( indicator ) ) {
				console.log( '[BANTU Plus] Potential piracy tool detected' );
				logAccess( 'suspicious_tool_detected' );
			}
		} );

		// Monitor for unusual network patterns
		if ( window.performance && window.performance.getEntriesByType ) {
			const resources = window.performance.getEntriesByType( 'resource' );
			resources.forEach( resource => {
				if ( resource.name.includes( '.mp4' ) || resource.name.includes( '.webm' ) ) {
					logAccess( 'direct_video_access' );
				}
			} );
		}
	}

	// ============================================
	// Initialize All Protections
	// ============================================
	function initialize() {
		console.log( '[BANTU Plus] Initializing anti-piracy protections...' );

		disableRightClick();
		disableDownloads();
		disableScreenRecording();
		preventContentCopy();
		monitorPlayback();
		detectPiracyTools();

		// Check concurrent streams
		const videoId = document.getElementById( 'bantu-player-container' )?.dataset.videoId;
		if ( videoId ) {
			checkConcurrentStreams( videoId );
		}

		console.log( '[BANTU Plus] Anti-piracy protections initialized' );
	}

	// ============================================
	// Check Concurrent Streams
	// ============================================
	function checkConcurrentStreams( videoId ) {
		const data = new FormData();
		data.append( 'action', 'bantu_check_stream' );
		data.append( 'video_id', videoId );
		data.append( 'nonce', bantuAntiPiracy.nonce );

		fetch( bantuAntiPiracy.ajaxUrl, {
			method: 'POST',
			body: data,
		} )
		.then( res => res.json() )
		.then( response => {
			if ( ! response.success ) {
				console.error( '[BANTU Plus] Stream limit exceeded' );
				alert( 'You have exceeded the maximum concurrent streams allowed for your account.' );
				// Pause video
				document.getElementById( 'bantu-player' )?.pause();
			}
		} )
		.catch( err => console.log( '[BANTU Plus] Stream check error:', err ) );
	}

	// ============================================
	// Watermark Overlay (Optional)
	// ============================================
	function addWatermarkOverlay() {
		const container = document.getElementById( 'bantu-player-container' );
		if ( ! container ) return;

		// Check if watermark is enabled
		const hasWatermark = container.dataset.hlsUrl && container.dataset.hlsUrl.includes( 'watermark' );
		if ( ! hasWatermark ) return;

		// Create visible watermark div as backup
		const watermark = document.createElement( 'div' );
		watermark.style.cssText = `
			position: absolute;
			top: 50%;
			left: 50%;
			transform: translate(-50%, -50%) rotate(-45deg);
			font-size: 80px;
			font-weight: bold;
			color: rgba(255, 255, 255, 0.1);
			white-space: nowrap;
			pointer-events: none;
			z-index: 10;
			font-family: Arial, sans-serif;
			text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
		`;
		watermark.textContent = 'CONFIDENTIAL';

		container.style.position = 'relative';
		container.appendChild( watermark );
	}

	// ============================================
	// Initialize on Document Ready
	// ============================================
	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', initialize );
	} else {
		initialize();
	}

	// Public API
	window.bantuPiracyProtection = {
		logAccess: logAccess,
		checkConcurrent: checkConcurrentStreams,
	};

})();
