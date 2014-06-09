function GA_tag(category, label, noninteraction, widget_id) {
	ga('send', 'event', 'WHICHet', category, label, {'noninteraction': noninteraction});
	_gaq.push(['_trackEvent', 'WHICHet', category, label, 1, noninteraction]);
	
	if (label === 'Variation A Engagement') {
	}
};