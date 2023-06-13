import * as React from 'react';

export function GeneralTable(props: {children?: React.ReactNode}) {
    return (
        <table className="table table-responsive-lg">
            { props.children }
        </table>
    )
}