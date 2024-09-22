import { useBookFormState } from '@/Features/Book/Context/BookFormContext';
import { useBlockDispatcher } from '@/Features/Book/Hooks/useBlockDispatcher';
import { PrimaryButton } from '@/UI/Components/Form/Button/PrimaryButton';
import { ButtonProps } from '@/UI/Types';
import { useBlockTypes } from '../Hooks/useBlockTypes';

const maxCustomBlockCount = 5;

export function AddCustomBlockButton(
  props: Omit<ButtonProps, 'onClick' | 'children'>,
) {
  const {
    data: { blocks },
  } = useBookFormState();
  const { isCustomBlock } = useBlockTypes();
  const { addCustomBlock } = useBlockDispatcher();
  const reachedMaxCount =
    blocks.upsert.filter((block) => isCustomBlock(block.type_id)).length >=
    maxCustomBlockCount;

  return (
    <PrimaryButton
      onClick={addCustomBlock}
      isDisabled={reachedMaxCount}
      {...props}
    >
      Add custom block
    </PrimaryButton>
  );
}
