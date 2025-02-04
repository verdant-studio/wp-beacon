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

	return (
		<Base title={__('NocoDB Settings', 'flying-beacon')}>
			<div className="flybe-gap-2 flybe-grid md:flybe-grid-cols-4">
				<div className="flybe-flex flybe-items-center flybe-text-left flybe-font-semibold">
					{__('URL', 'flying-beacon')}
				</div>
				<div className="flybe-col-span-3">
					<input
						className="flybe-w-full"
						name="url"
						onChange={(e) => handleChange('url', e.target.value)}
						placeholder="e.g. https://app.nocodb.com/api/v2/tables"
						type="text"
						value={settings.url}
					/>
				</div>
			</div>

			<div className="flybe-gap-2 flybe-grid md:flybe-grid-cols-4">
				<div className="flybe-flex flybe-items-center flybe-text-left flybe-font-semibold">
					{__('Table ID', 'flying-beacon')}
				</div>
				<div className="flybe-col-span-3">
					<input
						className="flybe-w-full"
						name="table_id"
						onChange={(e) => handleChange('table_id', e.target.value)}
						type="text"
						value={settings.table_id}
					/>
				</div>
			</div>

			<div className="flybe-gap-2 flybe-grid md:flybe-grid-cols-4">
				<div className="flybe-flex flybe-items-center flybe-text-left flybe-font-semibold">
					{__('XC token', 'flying-beacon')}
				</div>
				<div className="flybe-col-span-3">
					<input
						className="flybe-w-full"
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
