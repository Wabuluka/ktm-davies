import { EditButton } from '@/UI/Components/Form/Button/EditButton';
import { Radio, Td, Tr } from '@chakra-ui/react';
import { Creator } from '../Types';

type Props = {
  creator: Creator;
  selectable?: boolean;
  selectType?: 'none' | 'radio';
  onEdit: (creator: Creator) => void;
};

export function CreatorListItem({
  creator,
  selectable,
  selectType = 'none',
  onEdit,
}: Props) {
  function handleEditButtonClick() {
    onEdit(creator);
  }

  return (
    <Tr>
      {selectType !== 'none' && (
        <Td p={0}>
          {selectType === 'radio' && (
            <Radio
              value={`${creator.id}`}
              size="lg"
              p={4}
              isDisabled={!selectable}
            />
          )}
        </Td>
      )}
      <Td>
        {creator.name} {!!creator.name_kana && <>({creator.name_kana})</>}
      </Td>
      <Td>
        <EditButton
          onClick={handleEditButtonClick}
          aria-label={`Edit ${creator.name}`}
        />
      </Td>
    </Tr>
  );
}
