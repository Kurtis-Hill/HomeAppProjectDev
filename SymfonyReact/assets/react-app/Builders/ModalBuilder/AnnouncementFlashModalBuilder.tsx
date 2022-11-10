import * as React from 'react';

import { AnnouncementFlashModal } from '../../Components/Modals/AnnouncementFlashModal';

export function BuildAnnouncementFlashModal(props: { title: string; errors: string[]; errorNumber: number; }) {
  const title: string = props.title
  const errors: Array<string> = props.errors
  const errorNumber: number = props.errorNumber

  return (
        <AnnouncementFlashModal
            // modalShow={navbarRequestErrorModalShow} 
            title={title}
            errors={errors}
            errorNumber={errorNumber}
        />
  );
}