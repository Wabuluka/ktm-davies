import { useBlockDispatcher } from '@/Features/Book/Hooks/useBlockDispatcher';
import { DangerButton } from '@/UI/Components/Form/Button/DangerButton';
import { PrimaryButton } from '@/UI/Components/Form/Button/PrimaryButton';
import { Drawer } from '@/UI/Components/Overlay/Drawer';
import { Button, ButtonGroup, Text } from '@chakra-ui/react';
import { useId, useRef } from 'react';
import { BlockFormData, BlockOnBookForm } from '../Types';
import { CustomBlockForm } from './CustomBlockForm';

type Props = {
  customBlock: BlockOnBookForm;
  isOpen: boolean;
  onClose: () => void;
};

export function EditCustomBlockDrawer({ customBlock, isOpen, onClose }: Props) {
  const formId = useId();
  const firstInput = useRef(null);
  const { updateBlock, deleteBlock } = useBlockDispatcher();

  function handleDeleteButtonClick() {
    if (window.confirm('Are you sure to delete it?')) {
      deleteBlock(customBlock.id);
      onClose();
    }
  }
  function handleSubmit(formData: BlockFormData) {
    updateBlock(formData, customBlock.id);
    onClose();
  }

  return (
    <Drawer isOpen={isOpen} onClose={onClose} initialFocusRef={firstInput}>
      <Text>Edit Custom Block</Text>
      <CustomBlockForm
        id={formId}
        customBlock={customBlock}
        initialFocusRef={firstInput}
        onSubmit={handleSubmit}
      />
      <ButtonGroup>
        <Button onClick={onClose}>Back</Button>
        <DangerButton onClick={handleDeleteButtonClick}>Delete</DangerButton>
        <PrimaryButton type="submit" form={formId}>
          Save
        </PrimaryButton>
      </ButtonGroup>
    </Drawer>
  );
}
