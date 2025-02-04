/**
 * External dependencies.
 */
import React from 'react';

export function Base({ children, title }) {
	return (
		<div>
			<div className="flybe-bg-white flybe-rounded flybe-shadow flybe-max-w-screen-lg">
				<div className="flybe-border-b flybe-p-4">
					<h2 className="flybe-inline-block flybe-m-0">
						{title}
					</h2>
				</div>
				<div className="flybe-p-4 flybe-flex flybe-flex-col flybe-space-y-4">
					{children}
				</div>
			</div>
		</div>
	);
}
