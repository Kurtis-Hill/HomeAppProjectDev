import * as React from 'react';
import { useState } from 'react';

import SmallWhiteBoxDisplay from '../../../OldApp/js/components/DisplayBoxes/SmallWhiteBoxDisplay';
import CardFilterButton from '../Cards/Buttons/CardFilterButton';
// import { SensorDataContext } from '../../Contexts/SensorData/SensorDataContext';

export default function CardFilterBar() {
    const [showFilters, setShowFilters] = useState<boolean>(false);

    const itemDropdownToggleClass: string = showFilters === true ? 'show' : '';

    const toggleShowFilters = (): void => {
        setShowFilters(!showFilters);
    }

    const buildCardFilterForm = (): React => {
        return (
            <React.Fragment>
                <div style={{ padding: '2%' }} className="">
                    filter meee
                    filter meee
                    filter meee
                    filter meee
                    filter meee
                    filter meee
                    filter meee
                    filter meee
                    filter meee
                    filter meee
                    filter meee
                    filter meee
                    filter meee
                    filter meee
                </div>
        </React.Fragment>
        );
    }


    return (
        <div className="card-filter-bar-container">
            <CardFilterButton toggleShowFilters={toggleShowFilters} />

            {/* <SensorDataContext> */}
                <SmallWhiteBoxDisplay
                    classes={`${itemDropdownToggleClass} card-filter-box`}
                    heading={'Card Display Filters'}
                    content={ buildCardFilterForm() }
                />
           {/* </SensorDataContext> */}
        </div>
    );
}