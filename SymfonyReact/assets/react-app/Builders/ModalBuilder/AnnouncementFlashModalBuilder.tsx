import * as React from 'react';

import { AnnouncementFlashModal } from '../../Components/Modals/AnnouncementFlashModal';

export function BuildAnnouncementFlashModal(props: { title: string; errors: string[]; }) {
  const title: string = props.title
  const errors: Array<string> = props.errors

  return (
        <AnnouncementFlashModal
            // modalShow={navbarRequestErrorModalShow} 
            title={title}
            errors={errors}
        />
  );
}