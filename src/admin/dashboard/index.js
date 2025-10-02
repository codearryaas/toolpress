/**
 * Wordpress Dependencies
 */
import domReady from '@wordpress/dom-ready';
import { createRoot, render } from '@wordpress/element';

import { QueryClient, QueryClientProvider } from '@tanstack/react-query';

const queryClient = new QueryClient();

import App from './app';
import './styles/style.scss';

const AppInit = () => {
	return (
		<QueryClientProvider client={ queryClient }>
			<App />
		</QueryClientProvider>
	);
};

domReady( function () {
	const domElement = document.querySelector( '#toolpress-app' );

	if ( createRoot ) {
		createRoot( domElement ).render( <AppInit /> );
	} else {
		render( AppInit, domElement );
	}
} );
