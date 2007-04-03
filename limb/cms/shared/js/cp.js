function MarkAll(mark)
{
  var objects = document.getElementsByName('ids[]');

  if (objects != null)
     for (i = 0; i < objects.length; i++)
         objects[i].checked = mark;
}
