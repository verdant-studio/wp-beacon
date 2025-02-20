/**
 * External dependencies.
 */
import React, { useEffect, useState } from 'react';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies.
 */
import { Button } from '../components/Button';
import { FetchWP } from '../utils/FetchWP';
import { Airtable } from '../components/connectors/Airtable';
import { NocoDB } from '../components/connectors/NocoDB';

export function Settings({ wpObject }) {
	const DEFAULT_SCHEDULE = 'wp_beacon_12_hour';

	const [error, setError] = useState(null);
	const [saving, setSaving] = useState(false);
	const [schedules, setSchedules] = useState([]);
	const [settings, setSettings] = useState({
		schedule: DEFAULT_SCHEDULE,
		service: '',
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
				schedule: json.value.settings.schedule ?? DEFAULT_SCHEDULE,
				service: json.value.settings.service,
				service_settings: json.value.settings.service_settings,
			});

			setSchedules(json.value.schedules);
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
		<div className="wpbcn:flex wpbcn:flex-col wpbcn:space-y-4">
			<div className="wpbcn:bg-white wpbcn:rounded wpbcn:shadow wpbcn:max-w-screen-lg">
				<div className="wpbcn:border-b! wpbcn:border-slate-200! wpbcn:p-4">
					<h2 className="wpbcn:font-semibold wpbcn:inline-block wpbcn:m-0! wpbcn:text-lg!">
						{__('General Settings', 'wp-beacon')}
					</h2>
				</div>
				<div className="wpbcn:p-4 wpbcn:flex wpbcn:flex-col wpbcn:space-y-4">

					<div className="wpbcn:gap-2 wpbcn:grid md:wpbcn:grid-cols-4">
						<div className="wpbcn:flex wpbcn:items-center wpbcn:text-left wpbcn:font-semibold">
							{__('Service', 'wp-beacon')}
						</div>
						<div className="wpbcn:col-span-3">
							<select
								className="wpbcn:w-full"
								onChange={(e) =>
									setSettings({
										...settings,
										service: e.target.value,
									})
								}
								value={settings.service}
							>
								<option value="">{__('Choose a service', 'wp-beacon')}</option>
								<option value="airtable">Airtable</option>
								<option value="nocodb">NocoDB</option>
							</select>
						</div>
					</div>

					<div className="wpbcn:gap-2 wpbcn:grid md:wpbcn:grid-cols-4">
						<div className="wpbcn:flex wpbcn:items-center wpbcn:text-left wpbcn:font-semibold">
							{__('Schedule', 'wp-beacon')}
						</div>
						<div className="wpbcn:col-span-3">
							<select
								className="wpbcn:w-full"
								onChange={(e) =>
									setSettings({
										...settings,
										schedule: e.target.value,
									})
								}
								value={settings.schedule || DEFAULT_SCHEDULE}
							>
								{Object.entries(schedules).map(([key, value]) => (
									<option key={key} value={key}>
										{value}
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
			<div className="!wpbcn:mt-6">
				<Button
					loading={saving}
					onClick={updateSettings}
				>
					{__('Save Changes', 'wp-beacon')}
				</Button>
			</div>
		</div>
	);
}
