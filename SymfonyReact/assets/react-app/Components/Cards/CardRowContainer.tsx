import * as React from 'react';
import { CardReadingHandler } from './Readings/CardReadingHandler';
import CardFilterBar from '../Filterbars/CardFilterBar';

export function CardRowContainer(props: { route?: string; filterParams?: any[]; }) {
    const route: string = props.route ?? 'index';
    const filterParams: string[] = props.filterParams ?? [];


    return (
        <>
        <CardFilterBar />
                {/* <button
                style={{
                    position: 'absolute',
                    zIndex: '1',
                    right: '0px'
                }}
                >
                    <i className="fas fa-1x text-gray-300 fa-filter"></i>
                </button> */}
            <div className="row">
                {/* Created a filter bar option thing and stick it here */}
                <CardReadingHandler route={route} filterParams={filterParams} />
            </div>
        </>
    );
}