import * as React from 'react';
import { JsxElement } from 'typescript';

export function BaseCard(props: { children?: React.ReactNode; colour?: string; cardClasses?: string }): React {
    const colour: string = props.colour ?? 'primary';
    const cardClasses = props.cardClasses ?? 'col-xl-3 col-md-6 mb-4 hover'

    if (props.cardClasses) {
        return (
            <div className={`${cardClasses}`} key={1}>
                <div className={`shadow h-100 py-2 card border-left-${colour}`}>
                    <div className="card-body">
                        { props.children }
                    </div>
                </div>
            </div>
        );
    }

    return (
        <div className={`shadow h-100 py-2 card border-left-${colour}`}>
            <div className="card-body">
                { props.children }
            </div>
        </div>
    );
}