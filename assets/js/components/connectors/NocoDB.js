/**
 * External dependencies.
 */
import React, { useEffect, useState } from 'react';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies.
 */
import { Base } from './Base';

export function NocoDB({ data, update }) {
	const [settings, setSettings] = useState({
		url: '',
		table_id: '',
		xc_token: '',
	});

	// Set database data at render.
	useEffect(() => {
		console.log(data);
		setSettings({
			url: data?.url ?? '',
			table_id: data?.table_id ?? '',
			xc_token: data?.xc_token ?? '',
		})
	}, [data]);

	// Update parent state whenever the child state changes.
	const handleChange = (field, value) => {
		const updatedSettings = { ...settings, [field]: value };
		setSettings(updatedSettings);
		update(updatedSettings);
	};

	console.log(settings);

	return (
		<Base title={__('NocoDB Settings', 'wp-beacon')}>
			<div className="wpbcn:gap-2 wpbcn:grid md:wpbcn:grid-cols-4">
				<div className="wpbcn:flex wpbcn:items-center wpbcn:text-left wpbcn:font-semibold">
					{__('URL', 'wp-beacon')}
				</div>
				<div className="wpbcn:col-span-3">
					<input
						className="wpbcn:w-full"
						name="url"
						onChange={(e) => handleChange('url', e.target.value)}
						placeholder="e.g. https://app.nocodb.com/api/v2/tables"
						type="text"
						value={settings.url}
					/>
				</div>
			</div>

			<div className="wpbcn:gap-2 wpbcn:grid md:wpbcn:grid-cols-4">
				<div className="wpbcn:flex wpbcn:items-center wpbcn:text-left wpbcn:font-semibold">
					{__('Table ID', 'wp-beacon')}
				</div>
				<div className="wpbcn:col-span-3">
					<input
						className="wpbcn:w-full"
						name="table_id"
						onChange={(e) => handleChange('table_id', e.target.value)}
						type="text"
						value={settings.table_id}
					/>
				</div>
			</div>

			<div className="wpbcn:gap-2 wpbcn:grid md:wpbcn:grid-cols-4">
				<div className="wpbcn:flex wpbcn:items-center wpbcn:text-left wpbcn:font-semibold">
					{__('XC token', 'wp-beacon')}
				</div>
				<div className="wpbcn:col-span-3">
					<input
						className="wpbcn:w-full"
						name="xc_token"
						onChange={(e) => handleChange('xc_token', e.target.value)}
						type="text"
						value={settings.xc_token}
					/>
				</div>
			</div>
		</Base>
	);
}
