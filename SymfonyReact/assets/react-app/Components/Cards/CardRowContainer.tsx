import * as React from 'react';
import { CardReadingHandler } from './Readings/CardReadingHandler';


export function CardRowContainer(props: { route?: string; filterParams?: any[]; }) {
    const route: string = props.route ?? 'index';
    const filterParams: string[] = props.filterParams ?? [];


    return (
        <>
            <div className="row">
                {/* Created a filter bar option thing and stick it here */}
                <CardReadingHandler route={route} filterParams={filterParams} />
            </div>
        </>
    );
}