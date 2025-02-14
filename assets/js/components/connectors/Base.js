/**
 * External dependencies.
 */
import React from 'react';

export function Base({ children, title }) {
	return (
		<div>
			<div className="wpbcn:bg-white wpbcn:rounded wpbcn:shadow wpbcn:max-w-screen-lg">
				<div className="wpbcn:border-b! wpbcn:border-slate-200! wpbcn:p-4">
					<h2 className="wpbcn:font-semibold wpbcn:inline-block wpbcn:m-0! wpbcn:text-lg!">
						{title}
					</h2>
				</div>
				<div className="wpbcn:p-4 wpbcn:flex wpbcn:flex-col wpbcn:space-y-4">
					{children}
				</div>
			</div>
		</div>
	);
}
