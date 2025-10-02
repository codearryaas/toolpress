import WithId from './fields/with-id';
import TawkToChat from './fields/tawk-to-chat';

import {
	twbs,
	googleTag,
	googleTagManager,
	jQuery as jQueryLogo,
	fontAwesome,
	tawkTo,
	hubSpot,
} from '../../../components/icons';

export default {
	'google-tag-manager': {
		label: 'Google Tag Manager',
		description: 'Google Tag Manager Tag',
		logoIcon: googleTagManager,
		fieldsComponent: WithId,
	},
	'google-tag': {
		label: 'Google Tag',
		description: 'Google Tag',
		logoIcon: googleTag,
		fieldsComponent: WithId,
	},
	'hubspot-tracking': {
		label: 'HubSpot Tracking',
		description: 'HubSpot Tracking Code',
		logoIcon: hubSpot,
		fieldsComponent: WithId,
	},
	jquery: {
		label: 'jQuery',
		description: 'Load jQuery Library',
		logoIcon: jQueryLogo,
	},
	'font-awesome': {
		label: 'Font Awesome',
		description: 'Load Font Awesome Library',
		logoIcon: fontAwesome,
	},
	'tawk-to-chat': {
		label: 'Tawk.to Chat',
		description: 'Tawk.to Live Chat',
		logoIcon: tawkTo,
		fieldsComponent: TawkToChat,
	},
	twbs: {
		label: 'Bootstrap',
		description: 'Load Bootstrap Library',
		logoIcon: twbs,
	},
	'twbs-icons': {
		label: 'Bootstrap Icons',
		description: 'Load Bootstrap Icons Library',
		logoIcon: twbs,
	},
};
