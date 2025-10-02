import {
	__experimentalHStack as HStack,
	TextControl,
} from '@wordpress/components';
import { __ } from '@wordpress/i18n';

/**
 * External Dependencies
 */
import { Controller } from 'react-hook-form';

export default ( { formMethods: { control }, toolKey } ) => {
	return (
		<>
			<HStack>
				<label htmlFor="tawk-to-property-id">
					{ __( 'Property ID', 'toolpress' ) }
				</label>
				<Controller
					control={ control }
					name={ `${ toolKey }.property_id` }
					render={ ( { field } ) => (
						<TextControl
							{ ...field }
							id="tawk-to-property-id"
							label={ __( 'Property ID', 'toolpress' ) }
							hideLabelFromVision
							placeholder={ __(
								'Tawk.to Property ID',
								'toolpress'
							) }
						/>
					) }
				/>
			</HStack>
			<HStack>
				<label htmlFor="tawk-to-widget-id">
					{ __( 'Widget ID', 'toolpress' ) }
				</label>
				<Controller
					control={ control }
					name={ `${ toolKey }.widget_id` }
					render={ ( { field } ) => (
						<TextControl
							{ ...field }
							id="tawk-to-widget-id"
							label={ __( 'Widget ID', 'toolpress' ) }
							hideLabelFromVision
							placeholder={ __(
								'Tawk.to Widget ID',
								'toolpress'
							) }
						/>
					) }
				/>
			</HStack>
		</>
	);
};
