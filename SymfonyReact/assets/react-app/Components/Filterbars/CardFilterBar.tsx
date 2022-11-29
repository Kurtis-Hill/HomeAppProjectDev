import * as React from 'react';
import { useState } from 'react';

import SmallWhiteBoxDisplay from '../../../OldApp/js/components/DisplayBoxes/SmallWhiteBoxDisplay';

export default function CardFilterBar(props) {
    const [showFilters, setShowFilters] = useState(false)

    const itemDropdownToggleClass: string = showFilters === true ? 'show' : '';

    const toggleShowFilters = (): void => {
        setShowFilters(!showFilters);
    }

    return (
        <div>
            <button
            style={{
                position: 'absolute',
                zIndex: '1',
                right: '0px'
            }}
            onClick={() => {toggleShowFilters()}}
            >
                <i className="fas fa-1x text-gray-300 fa-filter"></i>
            </button>

            <SmallWhiteBoxDisplay
                dropdownToggleClass={itemDropdownToggleClass}
                heading={'Filters'}
                content={
                    <React.Fragment>
                        <div className="form-group">
                            filter meee
                        </div>
                    </React.Fragment>
                }
                />
           </div>
    );
}