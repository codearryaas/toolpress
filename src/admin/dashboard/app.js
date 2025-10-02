import {
	Panel,
	PanelHeader,
	PanelBody,
	__experimentalHStack as HStack,
	__experimentalVStack as VStack,
	ToggleControl,
	TextControl,
	Icon,
	Button,
	Card,
	CardBody,
	NoticeList,
	SnackbarList,
	CardHeader,
	CardFooter,
} from '@wordpress/components';
import { store as coreStore } from '@wordpress/core-data';
import { useSelect, useDispatch } from '@wordpress/data';
import { store as noticesStore } from '@wordpress/notices';
import { __ } from '@wordpress/i18n';

/**
 * External Dependencies
 */
import { useForm, Controller } from 'react-hook-form';

/**
 * Internal Dependencies.
 */
import { wrench } from '../../components/icons';
import tools from './configs/tools';
import { useEffect } from 'react';

export default function App() {
	const siteSettings = useSelect( ( select ) => {
		return select( coreStore ).getEntityRecord( 'root', 'site' );
	}, [] );

	const lastError = useSelect(
		( select ) => select( 'core' ).getLastEntitySaveError( 'root', 'site' ),
		[]
	);

	const { saveEntityRecord } = useDispatch( coreStore );

	// Use the `useDispatch` hook to get the action for removing notices.
	const { removeNotice, createNotice } = useDispatch( noticesStore );

	// Use the `useSelect` hook to retrieve all notices from the notices store.
	const notices = useSelect(
		( select ) => select( noticesStore ).getNotices(),
		[]
	);

	// Filter the notices to display only those intended for the snackbar.
	const snackbarNotices = notices.filter(
		( { type } ) => type === 'snackbar'
	);

	const toolsProperties = window?.toolpressData?.tools || {};
	const fields = {};
	Object.keys( toolsProperties ).forEach( ( toolKey ) => {
		fields[ toolKey ] = {
			...( toolsProperties[ toolKey ]?.default || {} ),
		};
		return null;
	} );

	const formMethods = useForm( {
		values: {
			...fields,
			...( siteSettings?.toolpress_tools_settings || {} ),
		},
	} );

	const {
		control,
		handleSubmit,
		watch,
		register,
		formState: { errors, dirtyFields },
	} = formMethods;

	const handleSaveSettings = async ( val ) => {
		Object.keys( val ).forEach( ( key ) => {
			const tool = val[ key ];

			if ( ! Object.keys( tool ).includes( 'enabled' ) ) {
				delete val[ key ];
			}
		} );

		const updatedSettings = {
			toolpress_tools_settings: { ...val },
		};
		try {
			const saveRecord = await saveEntityRecord( 'root', 'site', {
				...updatedSettings,
			} );
			if ( saveRecord ) {
				// Clear all existing notices to avoid duplicates.
				const noticeId = 'toolpress-settings-saved';
				removeNotice( noticeId );
				createNotice(
					'success',
					__( 'Settings saved successfully!', 'toolpress' ),
					{
						type: 'notice',
						id: noticeId, // Ensure a unique ID for this notice
					}
				);
				//  Goto top smoothly
				if ( 'scrollBehavior' in document.documentElement.style ) {
					window.scrollTo( { top: 0, behavior: 'smooth' } );
				} else {
					window.scrollTo( 0, 0 );
				}
			}
		} catch ( error ) {
			createNotice( 'error', error.message, { type: 'notice' } );
			console.error( 'Error saving settings:', error );
			// createNotice( 'error', error.message, { type: 'notice' } );
		}
	};

	useEffect( () => {
		if ( lastError ) {
			const noticeId = 'toolpress-save-error';
			removeNotice( noticeId );
			createNotice(
				'error',
				__( 'Error saving settings:', 'toolpress' ) +
					' ' +
					lastError.message,
				{
					type: 'notice',
					id: noticeId, // Ensure a unique ID for this notice
				}
			);
		}
	}, [ lastError ] );

	const changedSettings = watch();

	const hasDirtyFields = Object.keys( dirtyFields ).length > 0;
	// Filter the notices to display only those intended for the snackbar.
	const noticeList = notices.filter( ( { type } ) => type === 'notice' );

	return (
		<>
			<div className="wrap">
				<form onSubmit={ handleSubmit( handleSaveSettings ) }>
					<VStack spacing={ '2rem' }>
						<Card className={ 'toolpress-header-card' }>
							<CardBody>
								<h1>
									<HStack
										alignment="center"
										spacing="0.5rem"
										justify="flex-start"
									>
										<Icon icon={ wrench } />{ ' ' }
										<span>
											{ __( 'ToolPress', 'toolpress' ) }
										</span>
									</HStack>
								</h1>
								<p>
									{ __(
										'Welcome to the ToolPress admin dashboard!',
										'toolpress'
									) }
								</p>
							</CardBody>
						</Card>
						{ noticeList.length > 0 && (
							<NoticeList
								notices={ noticeList }
								onRemove={ removeNotice }
							/>
						) }
						<Card className={ 'toolpress-settings-card' }>
							<CardHeader>
								<HStack
									alignment="center"
									justify="space-between"
									spacing="1rem"
								>
									<strong>
										{ __( 'Tools Settings', 'toolpress' ) }
									</strong>
									<Button
										type="submit"
										variant="primary"
										disabled={ ! hasDirtyFields }
									>
										{ __( 'Save', 'toolpress' ) }
									</Button>
								</HStack>
							</CardHeader>
							<CardBody style={ { padding: 0 } }>
								<Panel style={ { border: '0px' } }>
									{ Object.keys( toolsProperties ).map(
										( toolKey ) => {
											const Tool = tools[ toolKey ];
											const toolProperties =
												toolsProperties[ toolKey ];
											return (
												<PanelBody
													key={ toolKey }
													initialOpen={ false }
													title={
														<HStack>
															<HStack
																alignment="center"
																spacing="0.5rem"
																justify="flex-start"
															>
																<Icon
																	icon={
																		Tool.logoIcon
																	}
																	size={ 24 }
																/>
																<span>
																	{
																		toolProperties.label
																	}
																</span>
															</HStack>
															{ changedSettings?.[
																toolKey
															]?.enabled && (
																<Icon
																	icon={
																		'yes-alt'
																	}
																	size={ 16 }
																	style={ {
																		color: 'green',
																	} }
																/>
															) }
														</HStack>
													}
												>
													<VStack
														spacing={ '1.5rem' }
														style={ {
															marginTop: '1rem',
														} }
													>
														<HStack>
															<label
																htmlFor={ `${ toolKey }-enabled` }
															>
																{ __(
																	'Enable',
																	'toolpress'
																) }
															</label>
															<Controller
																control={
																	control
																}
																{ ...register(
																	`${ toolKey }.enabled`
																) }
																render={ ( {
																	field: {
																		onChange,
																		value,
																		onBlur,
																	},
																} ) => (
																	<ToggleControl
																		// id={ `${ toolKey }-enabled` }
																		// label={ __(
																		// 	'Enable',
																		// 	'toolpress'
																		// ) }
																		hideLabelFromVision
																		checked={
																			!! value
																		}
																		onChange={ () => {
																			const snackbarId = `toolpress-changed`;
																			removeNotice(
																				snackbarId
																			);
																			createNotice(
																				'info',
																				`${
																					toolProperties.label
																				} ${ __(
																					"setting changed. Don't forget to save your changes!",
																					'toolpress'
																				) }`,
																				{
																					type: 'snackbar',
																					id: snackbarId, // Unique ID for this notice
																				}
																			);
																			onChange(
																				! value
																			);
																		} }
																		onBlur={
																			onBlur
																		}
																		__nextHasNoMarginBottom
																	/>
																) }
															/>
														</HStack>
														{ changedSettings[
															toolKey
														]?.enabled &&
														Tool.fieldsComponent ? (
															<Tool.fieldsComponent
																formMethods={
																	formMethods
																}
																toolKey={
																	toolKey
																}
															/>
														) : null }
													</VStack>
												</PanelBody>
											);
										}
									) }
								</Panel>
							</CardBody>
							<CardFooter>
								<HStack
									alignment="center"
									justify="flex-end"
									spacing="1rem"
								>
									<Button
										type="submit"
										variant="primary"
										disabled={ ! hasDirtyFields }
									>
										{ __( 'Save', 'toolpress' ) }
									</Button>
								</HStack>
							</CardFooter>
						</Card>
					</VStack>
				</form>
			</div>
			<SnackbarList
				notices={ snackbarNotices }
				onRemove={ removeNotice }
			/>
		</>
	);
}
