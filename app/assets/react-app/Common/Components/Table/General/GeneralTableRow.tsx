import * as React from 'react';

export function GeneralTableRow(props: {children?: React.ReactNode}) {
    return (
        <>
            <td
                // scope="row"
            >
                { props.children }
            </td>
        </>
    )
}
