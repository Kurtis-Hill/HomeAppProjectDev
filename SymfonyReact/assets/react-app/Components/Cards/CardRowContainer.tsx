import * as React from 'react';
import { Outlet } from 'react-router-dom';

export function CardRowContainer(props) {
    const content = props.content;
    return (
        <div className="row">
            {content}
        </div>
    );
}