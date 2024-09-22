import { useDisclosure } from '@chakra-ui/react';
import { useCallback } from 'react';
import { CreateExternalLinkDrawer } from '../Conponents/CreateExternalLinkDrawer';
import { ExternalLinkFormData } from '../Types';

type Props = {
  onStoreSuccess: (externalLink: ExternalLinkFormData) => void;
};

export function useCreateExternalLinkDrawer({ onStoreSuccess }: Props) {
  const disclosure = useDisclosure();
  const handleUpdateSuccess = useCallback(
    (externalLink: ExternalLinkFormData) => {
      onStoreSuccess(externalLink);
      disclosure.onClose();
    },
    [onStoreSuccess, disclosure],
  );
  const createExternalLinkDrawer = (
    <CreateExternalLinkDrawer
      isOpen={disclosure.isOpen}
      onClose={disclosure.onClose}
      onStoreSuccess={handleUpdateSuccess}
    />
  );

  return {
    ...disclosure,
    createExternalLinkDrawer,
  };
}
