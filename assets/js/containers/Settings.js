/**
 * External dependencies.
 */
import React, { useEffect, useState } from 'react';
import { __, sprintf } from '@wordpress/i18n';

/**
 * Internal dependencies.
 */
import { Button } from '../components/Button';
import { FetchWP } from '../utils/FetchWP';
import { Airtable } from '../components/connectors/Airtable';
import { NocoDB } from '../components/connectors/NocoDB';

const intervalOptions = [
	{
		label: __('Choose an interval', 'flying-beacon'),
		value: '',
	},
	{
		label: __('Hourly', 'flying-beacon'),
		value: 'hourly',
	},
	{
		label: __('Twice daily', 'flying-beacon'),
		value: 'twicedaily',
	},
	{
		label: __('Daily', 'flying-beacon'),
		value: 'daily',
	},
	{
		label: __('Weekly', 'flying-beacon'),
		value: 'weekly',
	},
];

export function Settings({ wpObject }) {
	const [error, setError] = useState(null);
	const [saving, setSaving] = useState(false);
	const [settings, setSettings] = useState({
		service: '',
		interval: '',
		service_settings: [],
	});

	// Create fetchWP instance.
	const fetch = new FetchWP({
		restURL: wpObject.ajax_base,
		restNonce: wpObject.nonce,
	});

	// Retrieve the settings.
	const getSettings = () => {
		return fetch.get('/settings').then((json) => {
			setSettings({
				service: json.value.service,
				interval: json.value.interval,
				service_settings: json.value.service_settings,
			});
		});
	};

	// Update the settings.
	const updateSettings = () => {
		setError(null);
		setSaving(true);

		return fetch
			.put('/settings', settings)
			.then(
				(err) => setError(err.message)
			)
			.finally(() => setSaving(false));
	};

	// Fire at render.
	useEffect(() => {
		getSettings();
	}, []);

	return (
		<div className="flybe-flex flybe-flex-col flybe-space-y-4">
			<div className="flybe-bg-white flybe-rounded flybe-shadow flybe-max-w-screen-lg">
				<div className="flybe-border-b flybe-p-4">
					<h2 className="flybe-inline-block flybe-m-0">
						{__('General Settings', 'flying-beacon')}
					</h2>
				</div>
				<div className="flybe-p-4 flybe-flex flybe-flex-col flybe-space-y-4">

					<div className="flybe-gap-2 flybe-grid md:flybe-grid-cols-4">
						<div className="flybe-flex flybe-items-center flybe-text-left flybe-font-semibold">
							{__('Service', 'flying-beacon')}
						</div>
						<div className="flybe-col-span-3">
							<select
								className="flybe-w-full"
								onChange={(e) =>
									setSettings({
										...settings,
										service: e.target.value,
									})
								}
								value={settings.service}
							>
								<option value="">{__('Choose a service', 'flying-beacon')}</option>
								<option value="airtable">Airtable</option>
								<option value="nocodb">NocoDB</option>
							</select>
						</div>
					</div>

					<div className="flybe-gap-2 flybe-grid md:flybe-grid-cols-4">
						<div className="flybe-flex flybe-items-center flybe-text-left flybe-font-semibold">
							{__('Interval', 'flying-beacon')}
						</div>
						<div className="flybe-col-span-3">
							<select
								className="flybe-w-full"
								onChange={(e) =>
									setSettings({
										...settings,
										interval: e.target.value,
									})
								}
								value={settings.interval}
							>
								{intervalOptions.map((option, index) => (
									<option key={index} value={option.value}>
										{option.label}
									</option>
								))}
							</select>
						</div>
					</div>

				</div>
			</div>
			{settings.service === 'airtable' && (<Airtable />)}
			{settings.service === 'nocodb' && (
				<NocoDB
					data={settings.service_settings}
					update={(updatedData) =>
						setSettings({
							...settings,
							service_settings: updatedData,
						})
					}
				/>
			)}
			<div className="!flybe-mt-6">
				<Button
					loading={saving}
					onClick={updateSettings}
				>
					{__('Save Changes', 'flying-beacon')}
				</Button>
			</div>
		</div>
	);
}
