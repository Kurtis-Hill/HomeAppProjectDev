import * as React from 'react';
import { useState } from 'react';

export default function CardFilterButton(props: { toggleShowFilters: () => void; }): React {
    const toggleShowFilters: () => void = props.toggleShowFilters;

    return (
        <>
            <button className="card-filter-button" onClick={toggleShowFilters}>
                <i className="fas fa-1x text-gray-300 fa-filter"></i>
            </button>
        </>
    );
}