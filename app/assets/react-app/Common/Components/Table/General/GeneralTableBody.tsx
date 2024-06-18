import * as React from 'react';

export function GeneralTableBody(props: {children?: React.ReactNode}) {
    return (
        <tbody>
            <tr>
                { props.children }                
            </tr>
        </tbody>
    )
}