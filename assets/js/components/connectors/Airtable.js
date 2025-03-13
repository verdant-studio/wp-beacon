/**
 * External dependencies.
 */
import React, { useEffect, useState } from 'react';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies.
 */
import { Base } from './Base';

export function Airtable({ data, disabled, update }) {
	const [settings, setSettings] = useState({
		url: '',
		base_id: '',
		table_id: '',
		api_key: '',
	});

	// Set database data at render.
	useEffect(() => {
		setSettings({
			url: data?.url ?? '',
			base_id: data?.base_id ?? '',
			table_id: data?.table_id ?? '',
			api_key: data?.api_key ?? '',
		})
	}, [data]);

	// Update parent state whenever the child state changes.
	const handleChange = (field, value) => {
		const updatedSettings = { ...settings, [field]: value };
		setSettings(updatedSettings);
		update(updatedSettings);
	};

	return (
		<Base title={__('Airtable Settings', 'wp-beacon')}>
			<div className="wpbcn:gap-2 wpbcn:grid md:wpbcn:grid-cols-4">
				<div className="wpbcn:flex wpbcn:items-center wpbcn:text-left wpbcn:font-semibold">
					{__('URL', 'wp-beacon')}
				</div>
				<div className="wpbcn:col-span-3">
					<input
						className="wpbcn:w-full"
						disabled={disabled}
						name="url"
						onChange={(e) => handleChange('url', e.target.value)}
						placeholder="e.g. https://api.airtable.com"
						type="text"
						value={settings.url}
					/>
				</div>
			</div>

			<div className="wpbcn:gap-2 wpbcn:grid md:wpbcn:grid-cols-4">
				<div className="wpbcn:flex wpbcn:items-center wpbcn:text-left wpbcn:font-semibold">
					{__('Base ID', 'wp-beacon')}
				</div>
				<div className="wpbcn:col-span-3">
					<input
						className="wpbcn:w-full"
						disabled={disabled}
						name="base_id"
						onChange={(e) => handleChange('base_id', e.target.value)}
						type="text"
						value={settings.base_id}
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
						disabled={disabled}
						name="table_id"
						onChange={(e) => handleChange('table_id', e.target.value)}
						type="text"
						value={settings.table_id}
					/>
				</div>
			</div>

			<div className="wpbcn:gap-2 wpbcn:grid md:wpbcn:grid-cols-4">
				<div className="wpbcn:flex wpbcn:items-center wpbcn:text-left wpbcn:font-semibold">
					{__('API key', 'wp-beacon')}
				</div>
				<div className="wpbcn:col-span-3">
					<input
						className="wpbcn:w-full"
						disabled={disabled}
						name="api_key"
						onChange={(e) => handleChange('api_key', e.target.value)}
						type="password"
						value={settings.api_key}
					/>
				</div>
			</div>
		</Base>
	);
}
