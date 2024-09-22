import { useDisclosure } from '@chakra-ui/react';
import { SelectExternalLinkDrawer } from '../Conponents/SelectExternalLinkDrawer';
import { ExternalLink } from '../Types';

type Props = {
  onSubmit: (externalLink: ExternalLink) => void;
};

export function useSelecExternalLinkDrawer({ onSubmit }: Props) {
  const disclosure = useDisclosure();

  const selectExternalLinkDrawer = (
    <SelectExternalLinkDrawer
      isOpen={disclosure.isOpen}
      onClose={disclosure.onClose}
      onSubmit={onSubmit}
    />
  );

  return {
    ...disclosure,
    selectExternalLinkDrawer,
  };
}
