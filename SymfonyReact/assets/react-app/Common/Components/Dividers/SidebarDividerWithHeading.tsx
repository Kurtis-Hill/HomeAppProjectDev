import * as React from 'react';

export function SidebarDividerWithHeading(props: { heading?: string; }) {
    const heading: string = props.heading ?? '';

    return (
        <React.Fragment>
            <hr className="sidebar-divider" />
            <div className="sidebar-heading">
                {heading}
            </div>
        </React.Fragment>
    );
}
