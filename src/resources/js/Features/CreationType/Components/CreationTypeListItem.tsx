import { EditButton } from '@/UI/Components/Form/Button/EditButton';
import { SortButtons } from '@/UI/Components/Form/Button/SortButtons';
import { HStack, Radio, Td, Tr } from '@chakra-ui/react';
import { useSortCreationTypeMutation } from '../Hooks/useSortCreationTypeMutation';
import { CreationType } from '../Types';

type Props = {
  creationType: CreationType;
  selectType?: 'none' | 'radio';
  firstItem?: boolean;
  lastItem?: boolean;
  onEdit: (creationType: CreationType) => void;
};

export function CreationTypeListItem({
  creationType,
  selectType = 'none',
  firstItem = false,
  lastItem = false,
  onEdit,
}: Props) {
  const { moveDownMutation, moveUpMutation } = useSortCreationTypeMutation();
  const isLoading = moveDownMutation.isLoading || moveUpMutation.isLoading;

  function handleOrderDown() {
    moveDownMutation.mutate(creationType.name);
  }
  function handleOrderUp() {
    moveUpMutation.mutate(creationType.name);
  }
  function handleEditButtonClick() {
    onEdit(creationType);
  }

  return (
    <Tr>
      {selectType !== 'none' && (
        <Td p={0}>
          {selectType === 'radio' && (
            <Radio value={`${creationType.name}`} size="lg" p={4} />
          )}
        </Td>
      )}
      <Td>{creationType.name}</Td>
      <Td>
        <HStack>
          <SortButtons
            disableUp={isLoading || firstItem}
            disableDown={isLoading || lastItem}
            onUp={handleOrderUp}
            onDown={handleOrderDown}
          />
          <EditButton
            onClick={handleEditButtonClick}
            aria-label={`Edit ${creationType}`}
          />
        </HStack>
      </Td>
    </Tr>
  );
}
