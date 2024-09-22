import { useLabelTypes } from '@/Features/LabelType/Hooks/useLabelTypes';
import { LabelType } from '@/Features/LabelType/Types';
import { PrimaryButton } from '@/UI/Components/Form/Button/PrimaryButton';
import { CloseIcon } from '@chakra-ui/icons';
import {
  Box,
  BoxProps,
  HStack,
  IconButton,
  List,
  ListItem,
  Select,
  Text,
} from '@chakra-ui/react';
import { useState } from 'react';

type Props = Omit<BoxProps, 'onSelect'> & {
  name?: string;
  value: string[];
  onSelect: (value: LabelType) => void;
  onUnselect: (value: LabelType) => void;
};

export function LabelTypesSelectBox({
  name,
  value,
  onSelect,
  onUnselect,
  ...props
}: Props) {
  const [selectedId, setSelectedId] = useState('');
  const types = useLabelTypes();
  const selectedTypes = types.filter((type) =>
    value.includes(type.id.toString()),
  );
  const unselectedTypes = types.filter(
    (type) => !value.includes(type.id.toString()),
  );
  function handleAddButtonClick() {
    if (!selectedId) {
      return;
    }
    const type = types.find((type) => type.id === Number(selectedId));
    if (!type) {
      throw new Error(`type not found. (selectedId: ${selectedId})`);
    }
    onSelect(type);
  }

  return (
    <Box w="fit-content" {...props}>
      <List spacing={2}>
        {selectedTypes.map((type) => (
          <HStack as={ListItem} key={type.id} spacing={4}>
            <Text flex={1}>{type.name}</Text>
            <IconButton
              icon={<CloseIcon />}
              aria-label={`${type.name}のDeselect`}
              onClick={() => onUnselect(type)}
            />
          </HStack>
        ))}
      </List>
      {unselectedTypes.length > 0 && (
        <HStack mt={4} spacing={4}>
          <Select name={name} onChange={(e) => setSelectedId(e.target.value)}>
            <option value="">Please select</option>
            {unselectedTypes.map((type) => (
              <option key={type.id} value={type.id}>
                {type.name}
              </option>
            ))}
          </Select>
          <PrimaryButton onClick={handleAddButtonClick}>Add</PrimaryButton>
        </HStack>
      )}
    </Box>
  );
}
