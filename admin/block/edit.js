const { BlockControls }                      = wp.blockEditor;
const { __ }                                 = wp.i18n;
const { useBlockProps }                      = wp.blockEditor;
const { serverSideRender: ServerSideRender } = wp;
const {
	Disabled,
	ToolbarGroup,
	ToolbarButton,
} = wp.components;

import { oneTimeOppsIcon, flexibleOppsIcon } from './icons';

export default function Edit( { attributes, setAttributes } ) {

	const { showOneTime } = attributes;
	const blockProps      = useBlockProps();

	return (
		<div { ...blockProps }>
			<>
				<BlockControls>
					<ToolbarGroup>
						<ToolbarButton
							icon={ oneTimeOppsIcon }
							label={ __( 'Show One-Time Opportunities', 'wired-impact-volunteer-management' ) }
							onClick={ () => setAttributes( { showOneTime: true } ) }
							isPressed={ showOneTime === true}
						/>
						<ToolbarButton
							icon={ flexibleOppsIcon }
							title={ __( 'Show Flexible Opportunities', 'wired-impact-volunteer-management' ) }
							onClick={ () => setAttributes( { showOneTime: false } ) }
							isPressed={ showOneTime !== true }
						/>
					</ToolbarGroup>
				</BlockControls>

				<Disabled>
					<ServerSideRender
						block='wired-impact-volunteer-management/volunteer-opps'
						attributes={ attributes }
					/>
				</Disabled>
			</>
		</div>
	);
}