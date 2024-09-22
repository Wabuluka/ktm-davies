import { Table, TableContainer, Tbody, Th, Thead, Tr } from '@chakra-ui/react';
import { ExternalLink } from '../Types';
import { ExternalLinkListItem } from './ExternalLinkListItem';

type Props = {
  externalLinks: ExternalLink[];
  onLinkEdit: (externalLink: ExternalLink) => void;
  selectable?: boolean | ((externalLink: ExternalLink) => boolean);
};

export function ExternalLinkList({
  externalLinks,
  onLinkEdit,
  selectable = true,
}: Props) {
  function handleEditButtonClick(externalLink: ExternalLink) {
    onLinkEdit(externalLink);
  }

  return (
    <TableContainer>
      <Table>
        <Thead>
          <Tr>
            {selectable !== false && <Th w={1}>Select</Th>}
            <Th>Title</Th>
            <Th w={1}>URL</Th>
            <Th w={1}>Thumbnail</Th>
            <Th w={1}>Operation</Th>
          </Tr>
        </Thead>
        <Tbody>
          {externalLinks.map((externalLink) => (
            <ExternalLinkListItem
              key={externalLink.id}
              externalLink={externalLink}
              onEdit={handleEditButtonClick}
              selectable={selectable}
            />
          ))}
        </Tbody>
      </Table>
    </TableContainer>
  );
}
