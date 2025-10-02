import {
	__experimentalHStack as HStack,
	TextControl,
} from '@wordpress/components';
import { __ } from '@wordpress/i18n';

/**
 * External Dependencies
 */
import { Controller } from 'react-hook-form';

export default ( { formMethods: { control }, toolKey } ) => (
	<>
		<HStack>
			<label htmlFor={ `${ toolKey }-id` }>
				{ __( 'ID', 'toolpress' ) }
			</label>
			<Controller
				control={ control }
				name={ `${ toolKey }.id` }
				render={ ( { field } ) => (
					<TextControl
						{ ...field }
						id={ `${ toolKey }-id` }
						hideLabelFromVision
						placeholder={ __( 'ID', 'toolpress' ) }
						label={ __( 'ID', 'toolpress' ) }
					/>
				) }
			/>
		</HStack>
	</>
);
