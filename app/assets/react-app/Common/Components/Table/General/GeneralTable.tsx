import * as React from 'react';

export function GeneralTable(props: {children?: React.ReactNode}) {
    return (
        <div className="table-container">
            <table className="table table-responsive-lg">
                {props.children}
            </table>
        </div>
    )
}
