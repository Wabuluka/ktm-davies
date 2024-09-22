import { useDisclosure } from '@chakra-ui/react';
import { useCallback } from 'react';
import { EditExternalLinkDrawer } from '../Conponents/EditExternalLinkDrawer';
import { ExternalLink, ExternalLinkFormData } from '../Types';

type Props = {
  externalLink: ExternalLink | null;
  onDeleteSuccess: (externalLink: ExternalLink) => void;
  onUpdateSuccess: (externalLink: ExternalLinkFormData) => void;
};

export function useEditExternalLinkDrawer({
  externalLink,
  onDeleteSuccess,
  onUpdateSuccess,
}: Props) {
  const disclosure = useDisclosure();
  const handleDeleteSuccess = useCallback(
    (externalLink: ExternalLink) => {
      onDeleteSuccess(externalLink);
      disclosure.onClose();
    },
    [disclosure, onDeleteSuccess],
  );
  const handleUpdateSuccess = useCallback(
    (externalLink: ExternalLinkFormData) => {
      onUpdateSuccess(externalLink);
      disclosure.onClose();
    },
    [onUpdateSuccess, disclosure],
  );
  const editExternalLinkDrawer = externalLink && (
    <EditExternalLinkDrawer
      externalLink={externalLink}
      isOpen={disclosure.isOpen}
      onClose={disclosure.onClose}
      onDeleteSuccess={handleDeleteSuccess}
      onUpdateSuccess={handleUpdateSuccess}
    />
  );

  return {
    ...disclosure,
    editExternalLinkDrawer,
  };
}
