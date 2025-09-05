document.querySelectorAll('.like-widget').forEach(function(widget) {
	var url = widget.getAttribute('data-url');
	var api = window.location.origin + '/like_api.php?url=' + encodeURIComponent(url);
	var btn = widget.querySelector('.like-btn');
	var countSpan = widget.querySelector('.like-count');
	var starIcon = btn.querySelector('.star-icon');

	// Check if user already liked this post (backend)
	fetch(api + '&action=status', { credentials: 'include' })
		.then(r => r.json())
		.then(data => {
			if (data.liked) {
				btn.disabled = true;
				btn.dataset.liked = 'true';
				starIcon.textContent = '★';
				starIcon.style.color = '#FFD700';
			} else {
				starIcon.textContent = '☆';
				starIcon.style.color = '#FFD700';
			}
			countSpan.textContent = data.count;
		});

	btn.onclick = function() {
		fetch(api + '&action=like', { credentials: 'include' })
		.then(r => r.json())
		.then(data => {
			countSpan.textContent = data.count;
			btn.disabled = true;
			btn.dataset.liked = 'true';
			starIcon.textContent = '★';
			starIcon.style.color = '#FFD700';
		});
	};
});