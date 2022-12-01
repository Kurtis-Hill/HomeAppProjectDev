import * as React from 'react';
import { CardReadingHandler } from './Readings/CardReadingHandler';
import CardFilterBar from '../Filterbars/CardFilterBar';

export function CardRowContainer(props: { route?: string; filterParams?: string[]; horizontal?: boolean; classes?: string; }) {
    const route: string = props.route ?? 'index';
    const filterParams: string[] = props.filterParams ?? [];
    const horizontal: boolean = props.horizontal ?? false;
    const classes: string = props.classes ?? 'col-xl-12 col-md-12 mb-12';

    const containerStyle = (): React => {
        if (horizontal === true) {
            return (
                <div className={classes}>
                    { buildCardReadingHandler() }
                </div>                
            );
        } else {
            return (
                <>
                    { buildCardReadingHandler() }
                </>
            );
        }
    };

    const buildCardReadingHandler = (): React => {
        return (
            <CardReadingHandler route={route} filterParams={filterParams} />
        );
    }

    return (
        <>
            <CardFilterBar />
            { containerStyle() }
        </>
    );
}