import { useDisclosure } from '@chakra-ui/react';
import { useCallback } from 'react';
import { EditRelatedItemDrawer } from '../Components/EditRelatedItemDrawer';
import { RelatedItemFormData } from '../Types';

type Props = {
  relatedItem?: RelatedItemFormData;
  onSubmit: (relatedItem: RelatedItemFormData) => void;
};

export function useEditRelatedItemDrawer({ relatedItem, onSubmit }: Props) {
  const disclosure = useDisclosure();
  const handleSubmit = useCallback(
    (relatedItem: RelatedItemFormData) => {
      onSubmit(relatedItem);
    },
    [onSubmit],
  );
  const editRelatedItemDrawer = relatedItem && (
    <EditRelatedItemDrawer
      key={JSON.stringify(relatedItem)}
      relatedItem={relatedItem}
      isOpen={disclosure.isOpen}
      onClose={disclosure.onClose}
      onSubmit={handleSubmit}
    />
  );

  return {
    ...disclosure,
    editRelatedItemDrawer,
  };
}
