import { useDisclosure } from '@chakra-ui/react';
import { useCallback } from 'react';
import { AddRelatedItemDrawer } from '../Components/AddRelatedItemDrawer';
import { RelatedItemFormData } from '../Types';

type Props = {
  onSubmit: (relatedItem: RelatedItemFormData) => void;
};

export function useAddRelatedItemDrawer({ onSubmit }: Props) {
  const disclosure = useDisclosure();
  const handleSubmit = useCallback(
    (relatedItem: RelatedItemFormData) => {
      onSubmit(relatedItem);
    },
    [onSubmit],
  );
  const addRelatedItemDrawer = (
    <AddRelatedItemDrawer
      isOpen={disclosure.isOpen}
      onClose={disclosure.onClose}
      onSubmit={handleSubmit}
    />
  );

  return {
    ...disclosure,
    addRelatedItemDrawer,
  };
}
